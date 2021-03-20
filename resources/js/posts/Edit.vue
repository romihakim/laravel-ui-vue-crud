<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Edit Post
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="savePost" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label for="inputTitle" class="col-md-4 col-form-label text-md-right">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" :class="{'is-invalid' : validation.title}" id="inputTitle" v-model="post.title" required autocomplete="title" autofocus>
                                    <span class="invalid-feedback" role="alert" v-if="validation.title">
                                        <strong>{{ validation.title[0] }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSlug" class="col-md-4 col-form-label text-md-right">Slug</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputSlug" v-model="post.slug" autocomplete="slug" autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputExcerpt" class="col-md-4 col-form-label text-md-right">Excerpt</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" id="inputExcerpt" v-model="post.excerpt" rows="3" autofocus></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputContent" class="col-md-4 col-form-label text-md-right">Content</label>
                                <div class="col-md-6">
                                    <editor id="inputContent" v-model="post.content"></editor>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputImage" class="col-md-4 col-form-label text-md-right">Image</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control-file" id="inputImage" @change="selectFile">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="inputPublished" v-model="post.published">
                                        <label class="form-check-label" for="inputPublished">Published</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Save"><i class="fas fa-save"></i> Save</button>
                                    <router-link :to="{name: 'postIndex'}" class="btn btn-sm btn-secondary" title="Go Back"><i class="fas fa-undo"></i> Back</router-link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.tox-tinymce-aux {
    display: none !important;
}
</style>

<script>
    export default {
        data() {
            return {
                post: {},
                validation: [],
                image: null
            }
        },
        created() {
            if (!!this.$route.params.id) {
                this.showPost(this.$route.params.id);
            } else {
                this.$router.push({name: 'postIndex'});
            }
        },
        mounted() {
            this.unregisterTinyMCE();
        },
        methods: {
            showPost(id) {
                axios.get(`/api/post/${id}`).then(response => {
                    return response.data.data;
                }).then(data => {
                    this.post = data;
                });
            },
            savePost() {
                const data = new FormData();
                const item = JSON.parse(JSON.stringify(this.post));

                Object.keys(item).forEach(function(key){
                    data.set(key, item[key]);
                });

                data.set('published', (this.post.published ? 1 : 0));

                if (this.image) {
                    data.set('image', this.image);
                } else {
                    data.delete('image');
                }

                data.append('_method', 'PATCH');

                axios.post(`/api/post/${item.id}`, data).then(response => {
                    return response.data;
                }).then(data => {
                    this.$router.push({
                        name: 'postIndex',
                        params: {
                            message: data.message
                        }
                    });
                }).catch(error => {
                    this.validation = error.response.data.data;
                });
            },
            selectFile(event) {
                this.image = event.target.files[0];
            },
            unregisterTinyMCE() {
                window.setTimeout(function() {
                    if (document.getElementsByClassName("tox-tinymce-aux").length > 0) {
                        document.getElementsByClassName("tox-tinymce-aux")[0].remove();
                    }
                }, 500);
            }
        }
    }
</script>
