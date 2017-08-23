<template>
    <div class="image">
        <image-preview :preview="value" @close="$emit('input', null)" v-if="value"></image-preview>
        <div class="image-upload" v-else>
            <i class="material-icons" @click="clicked">add_circle</i>
            <label>
                <input type="file" accept="images/*" @change="upload" ref="fileInput">
            </label>

        </div>
        <small class="error-control" v-if="error && !value">{{error[0]}}</small>
    </div>
</template>

<script type="text/javascript">
    import ImagePreview from './ImagePreview.vue'

    export default {
        components: {
            ImagePreview
        },
        props: {
            value: {
                type: [String, File],
                default: null
            },
            error: {
                type: [Array, String],
                default: null
            }
        },
        methods: {
            upload(e) {
                const files = e.target.files
                if (files && files.length > 0) {
                    this.$emit('input', files[0])
                }
            },
            clicked() {
                const elem = this.$refs.fileInput
                elem.click()
            }
        }
    }
</script>
