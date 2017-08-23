<template>
    <div class="container">
        <div class="row">
            <div class="progress">
                <div class="indeterminate"  v-if="isLoading"></div>
            </div>
            <nav>
                <div class="nav-wrapper blue-grey darken-4">
                  <router-link to="/" class="brand-logo left">News App</router-link>
                  <ul id="nav-mobile" class="right">
                    <li>
                        <router-link to="/login" v-if="!check">Login</router-link>
                    </li>
                    <li>
                        <router-link to="/register" v-if="!check">Register</router-link>
                    </li>
                    <li>
                        <router-link :to="`/users/${auth.user_id}/news`" v-if="check">Your News</router-link>
                    </li>
                    <li>
                        <a @click.stop="logout" v-if="check">Logout</a>
                    </li>
                  </ul>
                </div>
            </nav>
        </div>

        <div class="flash flash-success" v-if="flash.success">
            {{ flash.success }}
        </div>

        <div class="flash flash-error" v-if="flash.error">
            {{ flash.error }}
        </div>


        <router-view
        @loading="setLoading"
        @stoploading="unsetLoading"
        ></router-view>

        <div class="row footer">
        <footer class="page-footer  blue-grey darken-4">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">NewsApp</h5>
                <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Useful Links</h5>
                <ul>
                  <li><a class="grey-text text-lighten-3" href="/">Home</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2014 Copyright <a href="http://amraly.net" target="_blank">amraly.net</a>
            </div>
          </div>
        </footer>
        </div>
    </div>
</template>

<script>
    import Flash from './helpers/flash'
    import Auth from './store/auth'
    import { post } from './helpers/api'

    export default {
        created() {
            Auth.initialize()
        },
        data() {
            return {
                auth: Auth.state,
                flash: Flash.state,
                isLoading: false
            }
        },
        computed: {
            check() {
                if (this.auth.api_token && this.auth.user_id) {
                    return true
                }
                return false
            }
        },
        methods: {
            logout() {
                post(`/api/logout`)
                    .then((res) => {
                        if (res.data.logged_out) {
                            Auth.remove()

                            Flash.setSuccess('GoodBy!')

                            this.$router.push('/login')
                        }
                    })
            },
            setLoading() {
                this.isLoading = true
            },
            unsetLoading() {
                this.isLoading = false
            }
        }
    }
</script>
