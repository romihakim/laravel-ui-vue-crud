<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Show Post
                    </div>

                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <label for="inputTitle" class="col-md-4 col-form-label text-md-right">Title</label>
                                <div class="col-md-6 col-form-label">
                                    {{ post.title }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSlug" class="col-md-4 col-form-label text-md-right">Slug</label>
                                <div class="col-md-6 col-form-label">
                                    {{ post.slug }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputExcerpt" class="col-md-4 col-form-label text-md-right">Excerpt</label>
                                <div class="col-md-6 col-form-label">
                                    {{ post.excerpt }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputContent" class="col-md-4 col-form-label text-md-right">Content</label>
                                <div class="col-md-6 col-form-label">
                                    {{ post.content }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputImage" class="col-md-4 col-form-label text-md-right">Image</label>
                                <div class="col-md-6 col-form-label">
                                    <img :src="'../storage/posts/' + post.image" width="80" v-if="post.image">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <label :class="{'text-black-50': (post.published === 0), 'text-success': (post.published === 1)}">
                                        <i class="fas fa-check"></i> {{ post.published === 0 ? 'Unpublished' : 'Published' }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
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

<script>
    export default {
        data() {
            return {
                post: {}
            }
        },
        created() {
            if (!!this.$route.params.id) {
                this.showPost(this.$route.params.id);
            } else {
                this.$router.push({name: 'postIndex'});
            }
        },
        methods: {
            showPost(id) {
                axios.get(`/api/post/${id}`).then(response => {
                    return response.data.data;
                }).then(data => {
                    this.post = data;
                });
            }
        }
    }
</script>
