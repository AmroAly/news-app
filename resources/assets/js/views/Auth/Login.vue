<template>
    <div>
    <div class="row">
        <div class="col m8 offset-m2 card-panel teal lighten-2 verified" v-if="verified">
            <h6>Your email has been verfied!. Please login using your credentials.</h6>
        </div>
    </div>

    <div class="row login">
        <form @submit.prevent="login" class="col m6 offset-m3 card blue-grey darken-1">
            <div class="card-content white-text">
            <h4>Login</h4>
            <div class="">
                <label>Email:</label>
                <input type="text" v-model="form.email">
                <small class="error-control" v-if="error.email">{{error.email[0]}}</small>
            </div>
            <div class="">
                <label>Password</label>
                <input type="password" v-model="form.password">
                <small class="error-control" v-if="error.password">{{error.password[0]}}</small>
            </div>
            </div>
             <div class="card-action">
                <button class="t btn-large" :disabled="isProcessing
                ">Login
                </button>
            </div>
        </form>
    </div>
    </div>
</template>

<script>
    import { get, post } from '../../helpers/api'
    import Flash from '../../helpers/flash'
    import Auth from '../../store/auth'

    export default {
        created() {
            if (this.$route.meta.hasToken && this.$route.params.token) {
                const { email, token } = this.$route.params
                get(`/api/register/confirm/${token}`)
                    .then((res) => {
                        this.verified = true
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            Flash.setError(err.response.data[0])
                        }
                    })
            }
        },
        data() {
            return {
                form: {
                    email: '',
                    password: ''
                },
                error: {},
                verified: false,
                isProcessing: false
            }
        },
        methods: {
            login() {
                this.isProcessing = true
                this.error = {}
                post(`/api/login`, this.form)
                    .then((res) => {
                        const { authenticated, api_token, user_id } = res.data;
                        if (authenticated) {
                            Auth.set(api_token, user_id)
                            Flash.setSuccess('Welcome back!')
                            this.$router.push(`/users/${user_id}/news`)
                        }
                        this.isProcessing = false
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            console.log(err.response.data);
                            if (err.response.data.verified === false) {
                                this.isProcessing = false
                                return Flash.setError('Please check your inbox and click the verfication link')
                            }
                            this.error = err.response.data
                        }
                        this.isProcessing = false
                    })
            }
        }
    }
</script>
