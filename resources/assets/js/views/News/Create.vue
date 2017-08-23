<template>
    <div class="row">
        <div class="col m8 offset-m2 create-post">
            <form @submit.prevent="createNewsPost">
                <div class="">
                    <image-upload v-model="form.image" :error="error.image"></image-upload>
                </div>
                <div class="">
                    <label>Title:</label>
                    <input type="text" v-model="form.title">
                    <small class="error-control" v-if="error.title">{{error.title[0]}}</small>
                </div>
                <div class="">
                    <label for="textarea1">Body:</label>
                    <textarea
                    v-model="form.body"
                     class="materialize-textarea"></textarea>
                     <small class="error-control" v-if="error.body">{{error.body[0]}}</small>
                </div>
                <div class="">
                    <button class="btn btn-large" :disabled="isProcessing">Publish</button>
                    <button class="btn btn-large  grey lighten-1" @click="$router.back()" :disabled="isProcessing">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import { post } from '../../helpers/api'
    import Flash from '../../helpers/flash'
    import ImageUpload from '../../components/ImageUpload.vue'

    export default {
        components: {
            ImageUpload
        },
        data() {
            return {
                form: {
                    image: '',
                    title: '',
                    body: ''
                },
                error: {},
                isProcessing: false
            }
        },
        methods: {
            createNewsPost() {
                this.isProcessing = true
                const { image, title, body } = this.form
                let formData = new FormData();
                formData.append('image', image)
                formData.append('title', title)
                formData.append('body', body)
                post(`/api/news/create`, formData)
                    .then((res) => {
                        if (res.data.published) {
                            Flash.setSuccess(res.data.message)
                            this.$router.push(`/news/${res.data.id}`)
                        }
                        this.isProcessing = false
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            this.error = err.response.data
                        }
                        this.isProcessing = false
                    })
            }
        }
    }
</script>
