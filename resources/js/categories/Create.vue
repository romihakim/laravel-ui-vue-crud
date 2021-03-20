<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Add New Category
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="saveCategory">
                            <div class="form-group row">
                                <label for="inputTitle" class="col-md-4 col-form-label text-md-right">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" :class="{'is-invalid' : validation.title}" id="inputTitle" v-model="category.title" required autocomplete="title" autofocus>
                                    <span class="invalid-feedback" role="alert" v-if="validation.title">
                                        <strong>{{ validation.title[0] }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSlug" class="col-md-4 col-form-label text-md-right">Slug</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputSlug" v-model="category.slug" autocomplete="slug" autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputDescription" class="col-md-4 col-form-label text-md-right">Description</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" id="inputDescription" v-model="category.description" rows="3" autofocus></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="inputPublished" v-model="category.published">
                                        <label class="form-check-label" for="inputPublished">Published</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Save"><i class="fas fa-save"></i> Save</button>
                                    <router-link :to="{name: 'categoryIndex'}" class="btn btn-sm btn-secondary" title="Go Back"><i class="fas fa-undo"></i> Back</router-link>
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
                category: {},
                validation: []
            }
        },
        methods: {
            saveCategory() {
                axios.post(`/api/category`, this.category).then(response => {
                    return response.data;
                }).then(data => {
                    this.$router.push({
                        name: 'categoryIndex',
                        params: {
                            message: data.message
                        }
                    });
                }).catch(error => {
                    this.validation = error.response.data.data;
                });
            }
        }
    }
</script>
