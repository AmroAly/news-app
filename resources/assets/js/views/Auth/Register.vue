<template>
    <div class="register row">
        <form @submit.prevent="register" class="col m6 offset-m3 card blue-grey darken-1">
            <div class="card-content white-text">
            <h4>Create An Account</h4>
            <div class="">
                <label>Name:</label>
                <input type="text" v-model="form.name">
                <small class="error-control" v-if="error.name">{{error.name[0]}}</small>
            </div>
            <div class="">
                <label>Email:</label>
                <input type="text" v-model="form.email">
                <small class="error-control" v-if="error.email">{{error.email[0]}}</small>
            </div>
            <div class="row">
                <div class="col s6">
                    <label>Password</label>
                    <input type="password" v-model="form.password">
                    <small class="error-control" v-if="error.password">{{error.password[0]}}</small>
                </div>
                <div class="col s6">
                    <label>Confirm Password</label>
                    <input type="password" v-model="form.password_confirmation">
                    <small class="error-control" v-if="error.password_confirmation">{{error.password_confirmation[0]}}</small>
                </div>
            </div>
            </div>
             <div class="card-action">
                <button class="t btn-large" :disabled="isProcessing
                ">Create
                </button>

            </div>
        </form>
    </div>
</template>

<script type="text/javascript">
    import Flash from '../../helpers/flash'
    import  { post } from '../../helpers/api'
    export default {
        data() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                error: {},
                isProcessing: false
            }
        },
        methods: {
            register() {
                this.$emit('loading');
                this.isProcessing = true
                this.error = {}
                post(`/api/register`, this.form)
                    .then((res) => {
                        if (res.data.registered) {
                            Flash.setSuccess('You have succesfully created an account!. Please check your inbox')
                        }
                        this.isProcessing = false
                        this.$emit('stoploading');
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            this.error = err.response.data
                        }
                        this.isProcessing = false
                        this.$emit('stoploading');
                    })
            }
        }
    }
</script>
