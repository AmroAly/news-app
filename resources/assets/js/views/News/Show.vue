<template>
    <div class="row">
        <not-found v-if="notFound"></not-found>
        <span v-else>
            <div class="col m3  left-div">
                <ul class="sidenav">
                  <li><a href="#!">First Sidebar Link</a></li>
                  <li><a href="#!">Second Sidebar Link</a></li>
                </ul>
            </div>
            <div class="col m6 s12 center-div">
              <div class="card">
                <div class="card-image">
                  <img :src="`/images/${newsPost.image}`">
                  <span class="card-title" v-if="newsPost.image">
                        {{newsPost.title}}
                  </span>
                </div>
                <div class="card-content">
                    <small class="post-info">Reporter: {{newsPost.user}} at {{newsPost.created_at}}</small>
                  <p>{{newsPost.body}}</p>
                </div>
              </div>
            </div>
            <div class="col m3 right-div">
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
                <a class="btn btn-large red darken-2" @click="deletePost" v-if="auth.api_token && auth.user_id == newsPost.user_id">
                    Delete
                </a>
                <!-- :href="`/api/pdf/${this.newsPost.id}`" -->
                <a class="btn btn-large indigo"
                @click="generatePdf"
                >
                    Download
                    <i class="material-icons right">file_download</i>
                </a>
            </div>
        </span>
    </div>
</template>

<script>
    import { get, del } from '../../helpers/api'
    import Flash from '../../helpers/flash'
    import Auth from '../../store/auth'
    import NotFound from '../NotFound.vue'

    export default {
        components: {
            NotFound
        },
        created() {
            this.$emit('loading');
            Auth.initialize()
            get(`/api/news/${this.$route.params.id}`)
                .then((res) => {
                    this.newsPost = res.data.newsPost
                    this.$emit('stoploading');
                })
                .catch((err) => {
                    if (err.response.status === 404) {
                        this.notFound = true
                    }
                    this.$emit('stoploading');
                })
        },
        data() {
            return {
                newsPost: {},
                auth: Auth.state,
                notFound: false,
                isProcessing: false
            }
        },
        methods: {
            deletePost() {
                del(`/api/news/${this.$route.params.id}`)
                    .then((res) => {
                        if (res.data.deleted) {
                            Flash.setSuccess('News deleted successfully!')
                            this.$router.push(`/users/${res.data.user_id}/news`)
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 401) {
                            this.$router.push('/')
                        }
                    })
            },
            generatePdf() {
                const link = document.createElement('a')
                link.href = `/api/pdf/${this.newsPost.id}`
                link.download="news.pdf";
                link.click();
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
