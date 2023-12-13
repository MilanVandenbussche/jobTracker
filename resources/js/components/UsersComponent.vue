<template>
    <div v-if="loading"
         class=" position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div v-else class="d-flex flex-column">
        <table class="table">
            <thead>
            <tr :class="[currentPage === 1 ? 'border-primary' : '']">
                <th scope="col"><span class="visually-hidden">#</span></th>
                <th scope="col"><span class="visually-hidden">Profile picture</span></th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Language</th>
                <th scope="col">Tags</th>
                <th scope="col">Joined</th>
                <th scope="col" class="text-center">Deleted at</th>
                <th scope="col" class="text-center" v-if="admin">Admin</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in paginatedData" :class="[user.id === auth_user_id ? 'border-primary' : '']">
                <th class="align-middle" scope="row"><span class="visually-hidden">{{ user.id }}</span></th>
                <td class="align-middle">
                    <div>
                        <img v-if="user.id !== auth_user_id" :src="user.profile_picture" :alt="user.name"
                             :title="user.name"
                             class="img-fluid sq-40 rounded-circle">
                        <button v-if="user.id === auth_user_id" class="btn btn-link p-0" data-bs-toggle="modal"
                                data-bs-target="#profilePictureModal">
                            <img :src="user.profile_picture" :alt="user.name" :title="user.name"
                                 class="img-fluid sq-40 rounded-circle">
                        </button>
                    </div>
                </td>
                <td class="align-middle">{{ user.name }}</td>
                <td class="align-middle">{{ user.email }}</td>
                <td class="align-middle">{{ user.language }}</td>
                <td class="align-middle">
                    <div class="d-flex justify-content-start align-items-center gap-1">
                        <span v-for="tag in user.tags" class="badge bg-primary rounded-pill">{{ tag.name }}</span>
                    </div>
                </td>
                <td class="align-middle">{{ user.created_at }}</td>
                <td class="align-middle text-center">{{ user.deleted_at ?? '-' }}</td>
                <td class="align-middle text-center" v-if="admin">
                    <button :key="user.id" v-if="user.admin" @click="toggleAdmin(user.id)" class="btn btn-link p-0" :disabled="user.id === auth_user_id">
                        <i class="fas fa-check text-success"></i>
                    </button>
                    <button :key="user.id" v-if="!user.admin" @click="toggleAdmin(user.id)" class="btn btn-link p-0">
                        <i class="fas fa-xmark text-muted"></i>
                    </button>
                </td>
                <td class="align-middle">
                    <div v-if="user.id !== auth_user_id" class="d-flex justify-content-end gap-2">
                        <button :key="user.id" v-if="!user.deleted_at" @click="favoriteUser(user.id)"
                                class="btn btn-link p-0"
                                :class="userFavoritesList.includes(user.id) ? 'text-warning' : 'text-muted'">
                            <i class="fas fa-star"></i>
                        </button>
                        <button :key="user.id" v-if="admin && !user.deleted_at" @click="deleteUser(user.id)"
                                class="btn btn-link p-0">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                        <button :key="user.id" v-if="admin && user.deleted_at" @click="restoreUser(user.id)"
                                class="btn btn-link p-0">
                            <i class="fas fa-recycle text-success"></i>
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="modal fade" data-bs-backdrop="static" id="profilePictureModal" tabindex="-1" aria-labelledby="profilePictureModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-3xl" id="profilePictureModal">Change profile picture</h1>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveImage">
                            <input @change="tempPhoto" type="file" accept="image/png, image/jpeg, image/jpg" name="profile_picture"
                                   id="profile_picture" class="visually-hidden">
                            <label for="profile_picture" class="d-flex justify-content-center">
                                <img v-if="!tempPhotoUrl" class="sq-3xl object-fit-cover rounded-3 cursor-pointer"
                                     :src="usersList[0].profile_picture_lg" alt="temp">
                                <img v-if="tempPhotoUrl" class="sq-3xl object-fit-cover rounded-3 cursor-pointer" :src="tempPhotoUrl"
                                     alt="">
                            </label>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <button @click="destroyTempPhoto" type="reset" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel
                                </button>
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                    Save
                                    <i v-if="!imageLoading" class="fas fa-save"></i>
                                    <span v-if="imageLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center ms-auto btn-group">
            <button @click="currentPage--" class="btn btn-outline-primary" :disabled="currentPage === 1">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button @click="currentPage = n" v-for="n in totalPages" class="btn"
                    :class="n === currentPage ? 'btn-primary' : 'btn-outline-primary'">{{ n }}
            </button>
            <button @click="currentPage++" class="btn btn-outline-primary" :disabled="currentPage === totalPages">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Users Component',
    emits: ['updateAuthUser'],
    data() {
        return {
            auth_user_id: null,
            loading: true,
            usersList: [],
            userFavoritesList: [],
            currentPage: 1,
            itemsPerPage: 12,

            tempPhotoUrl: null,
            file: null,
            imageLoading: false,
        }
    },
    computed: {
        paginatedData() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.usersList.slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.usersList.length / this.itemsPerPage);
        },
        admin() {
            return localStorage.admin === 'true';
        }
    },
    methods: {
        fetchUsers() {
            return axios.get('/api/user/all', {
                headers: {
                    Authorization: "Bearer " + localStorage.remember_token
                },
                params: {
                    favorites: true,
                }
            }).then(r => {
                this.usersList = r.data.users;
                this.userFavoritesList = r.data.favorites;
            }).catch(e => {
                console.error(e.response.data.message);
            }).finally(() => {
                this.loading = false;
            })
        },
        toggleAdmin(id) {
            if (id !== this.auth_user_id) {
                axios.post('/api/user/make-admin', {
                    id: id,
                }, {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.remember_token,
                    }
                }).then(r => {
                    this.fetchUsers();
                })
            }
        },
        favoriteUser(id) {
            axios.post('/api/user/favorite', {
                id: id,
            }, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.remember_token,
                },
            }).then(r => {
                this.fetchUsers()
            })
        },
        deleteUser(id) {
            axios.post('/api/user/delete', {
                id: id,
            }, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.remember_token,
                },
            }).then(r => {
                this.fetchUsers()
            })
        },
        restoreUser(id) {
            axios.post('/api/user/restore', {
                id: id,
            }, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.remember_token,
                },
            }).then(r => {
                this.fetchUsers();
            })
        },
        tempPhoto(e) {
            const file = e.target.files[0];
            this.file = file;
            this.tempPhotoUrl = URL.createObjectURL(file);
        },
        destroyTempPhoto() {
            URL.revokeObjectURL(this.tempPhotoUrl);
            this.tempPhotoUrl = null;
            this.file = null;
        },
        saveImage() {
            this.imageLoading = true;
            if (this.file) {
                const formData = new FormData();
                formData.append('file', this.file);

                axios.post('/api/user/image', formData, {
                    headers: {
                        Authorization:  "Bearer " + localStorage.remember_token
                    }
                })
                    .then(r => {
                        console.log(r.data)
                        this.fetchUsers();
                    })
                    .catch(e => {
                        console.log(e.response.data.message);
                    })
                    .finally(() => {
                        this.imageLoading = false;
                        this.$emit("updateAuthUser");
                    })
            }
        }
    },
    mounted() {
        this.fetchUsers();
        this.auth_user_id = parseInt(localStorage.user_id);
    },
}
</script>
