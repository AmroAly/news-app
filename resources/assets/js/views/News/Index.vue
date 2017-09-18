<template>
    <div class="row index">
        <div class="col m3 s12 left-div">
            <ul class="sidenav">
              <li><router-link to="/">Home</router-link></li>
               <li v-if="check"><router-link :to="`/users/${auth.user_id}/news`">Your Published News</router-link></li>
            </ul>
        </div>
        <div class="col m6 s12 center-div">
          <div class="card" v-for="post in news" v-if="news.length">
                <router-link :to="`/news/${post.id}`">

                    <div class="card-image">
                      <img :src="`/images/${post.image}`">
                      <span class="card-title" v-if="post.image">
                            {{post.title}}
                      </span>
                    </div>
                    <div class="card-content">
                        <small class="post-info">Reporter: {{post.user}} at {{post.created_at}}</small>
                        <p>{{sliceText(post.body)}}</p>
                    </div>
                    <div class="card-action">
                      <router-link :to="`/news/${post.id}`">Read More!</router-link>
                    </div>

                </router-link>
          </div>
          <div v-if="!news.length">
                <h6>You have no news at the moment</h6>
          </div>
        </div>
        <div class="col m3 s12 right-div">
            <a href="/feed" target="_blank" class="feed-icon">
                <i class="material-icons orange darken-1">rss_feed</i>
            </a>
            <router-link
            to="/news/create"
            class="btn btn-large"
            v-if="check"
            >
                Publish News<i class="material-icons right">create</i>
            </router-link>
            <router-link v-else
            to="/login"
            class="btn btn-large"
            >Login</router-link>
        </div>
    </div>
</template>

<script>
    import { get } from '../../helpers/api'
    import Auth from '../../store/auth'
    import Flash from '../../helpers/flash'

    export default {
        created() {
            this.$emit('loading')
            Auth.initialize()

            let url = ''
            if (this.$route.meta.type === 'currentUserNewsPosts') {
                url = `/api/users/${this.$route.params.id}/news`
            } else {
                url = `/api/news`
            }

            this.getNewsPosts(url)
        },
        data() {
            return {
                news: [],
                auth: Auth.state,
                isProcessing: false
            }
        },
        methods: {
            getNewsPosts(url) {
                get(url)
                    .then((res) => {
                        this.news = res.data
                        this.$emit('stoploading');
                    })
                    .catch((err) => {
                        if (err.response.status === 404) {
                            Flash.setError('You don\'t have any posts yet')
                            this.$router.push('/')
                        }
                        this.$emit('stoploading');
                    })
            },
            sliceText(str) {
                if (str.length > 100) {
                    return str.slice(0, 100) + '...'
                }
                return str
            }
        },
        watch: {
            '$route.params.id'(newId, oldId) {
                if (typeof newId === "undefined") {
                    return this.getNewsPosts(`/api/news`)
                }
                if (newId) {
                    return this.getNewsPosts(`/api/users/${this.$route.params.id}/news`)
                }
            }
        },
        computed: {
            check() {
                if (this.auth.api_token && this.auth.user_id) {
                    return true
                }
                return false
            }
        }
    }
</script>
