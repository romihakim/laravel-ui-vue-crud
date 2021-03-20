<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Posts
                        <router-link :to="{name: 'postCreate'}" class="btn btn-sm btn-success" title="Add New"><i class="fas fa-plus"></i> Add New</router-link>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-success" v-if="message">
                            {{ message }}
                        </div>
                        <table class="table table-striped table-sm">
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Published</th>
                                <th></th>
                            </tr>
                            <tr v-for="(post, index) in posts.data" :key="post.id">
                                <td>{{ posts.from + index }}</td>
                                <td>{{ post.title }}</td>
                                <td><img :src="'storage/posts/' + post.image" width="80" v-if="post.image"></td>
                                <td :class="{'text-black-50': (post.published === 0), 'text-success': (post.published === 1)}">
                                    <i class="fas fa-check"></i>
                                </td>
                                <td class="text-right">
                                    <router-link :to="{name: 'postShow', params: {id: post.id}}" class="btn btn-sm btn-info" title="Show"><i class="fas fa-eye"></i></router-link>
                                    <router-link :to="{name: 'postEdit', params: {id: post.id}}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-pen"></i></router-link>
                                    <button class="btn btn-sm btn-danger" title="Delete" @click = "deletePost(post.id)"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </table>
                        <div v-if="posts.total > 0">
                            <pagination :data="posts" @pagination-change-page="getPosts"></pagination>
                        </div>
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
                posts: [],
                message: null,
                current_page: null
            }
        },
        created() {
            this.getPosts();

            if (!!this.$route.params.message) {
                this.message = this.$route.params.message;
            }
        },
        methods: {
            getPosts(page = 1) {
                axios.get(`/api/post?page=${page}`).then(response => {
                    return response.data.data;
                }).then(data => {
                    this.posts = data;
                    this.current_page = data.current_page;

                    if (this.current_page > 1 && data.data.length == 1) {
                        this.current_page -= 1;
                    }
                });
            },
            deletePost(id) {
                if (confirm('Are you sure to delete?')) {
                    axios.delete(`/api/post/${id}`).then(response => {
                        this.message = response.data.message;
                        this.getPosts(this.current_page);
                    });
                }
            }
        }
    }
</script>
