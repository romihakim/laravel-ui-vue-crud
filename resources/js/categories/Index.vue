<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Categories
                        <router-link :to="{name: 'categoryCreate'}" class="btn btn-sm btn-success" title="Add New"><i class="fas fa-plus"></i> Add New</router-link>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-success" v-if="message">
                            {{ message }}
                        </div>
                        <table class="table table-striped table-sm">
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Published</th>
                                <th></th>
                            </tr>
                            <tr v-for="(category, index) in categories.data" :key="category.id">
                                <td>{{ categories.from + index }}</td>
                                <td>{{ category.title }}</td>
                                <td :class="{'text-black-50': (category.published === 0), 'text-success': (category.published === 1)}">
                                    <i class="fas fa-check"></i>
                                </td>
                                <td class="text-right">
                                    <router-link :to="{name: 'categoryShow', params: {id: category.id}}" class="btn btn-sm btn-info" title="Show"><i class="fas fa-eye"></i></router-link>
                                    <router-link :to="{name: 'categoryEdit', params: {id: category.id}}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-pen"></i></router-link>
                                    <button class="btn btn-sm btn-danger" title="Delete" @click = "deleteCategory(category.id)"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </table>
                        <div v-if="categories.total > 0">
                            <pagination :data="categories" @pagination-change-page="getCategories"></pagination>
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
                categories: [],
                message: null,
                current_page: null
            }
        },
        created() {
            this.getCategories();

            if (!!this.$route.params.message) {
                this.message = this.$route.params.message;
            }
        },
        methods: {
            getCategories(page = 1) {
                axios.get(`/api/category?page=${page}`).then(response => {
                    return response.data.data;
                }).then(data => {
                    this.categories = data;
                    this.current_page = data.current_page;

                    if (this.current_page > 1 && data.data.length == 1) {
                        this.current_page -= 1;
                    }
                });
            },
            deleteCategory(id) {
                if (confirm('Are you sure to delete?')) {
                    axios.delete(`/api/category/${id}`).then(response => {
                        this.message = response.data.message;
                        this.getCategories(this.current_page);
                    });
                }
            }
        }
    }
</script>
