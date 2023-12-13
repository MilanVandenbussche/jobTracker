<template>
    <div v-if="loading"
         class=" position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div v-else class="d-flex flex-column align-items-end gap-3 mb-5">
        <div>
            <button @click="this.$emit('addJob')" class="btn btn-outline-primary" v-if="admin">
                Add new job
            </button>
        </div>
        <article v-for="job in jobsList" class="p-3 border border-primary rounded-3 w-100 d-flex flex-column gap-3">
            <div>
            <h3 class="fs-6xl">{{ job.job_lang.job_title }}</h3>
                <div class="d-flex gap-2">
                    <span v-for="tag in job.tags" class="badge border border-primary rounded-pill text-capitalize px-3">
                    {{ tag.name}}
                </span>
                </div>
            </div>
            <div class="d-flex flex-column gap-3">
                <section>
                    <h4 class="fs-3xl">About the company</h4>
                    <p class="m-0">{{ job.job_lang.job_company}}</p>
                </section>
                <section>
                    <h4 class="fs-3xl">About the job</h4>
                    <p class="m-0">{{ job.job_lang.job_description}}</p>
                </section>
                <section>
                    <h4 class="fs-3xl">Qualifications</h4>
                    <p class="m-0 text-nowrap" v-html="job.job_lang.job_qualifications"></p>
                </section>
                <section>
                    <h4 class="fs-3xl">What we offer</h4>
                    <p class="m-0 text-nowrap" v-html="job.job_lang.job_offer"></p>
                </section>
            </div>
            <div class="d-flex gap-3">
                <div v-for="media in job.media">
                    <img :src="'assets/jobs/xl_' + media.media_name + '.' + media.media_ext" :alt="job.job_lang.job_title" class="object-fit-cover rounded-3 shadow">
                </div>
            </div>
            <div v-if="admin" class="d-flex justify-content-between align-items-center gap-3 mt-3">
                <div class="form-check form-switch">
                    <input @change="toggleActive(job.id)" class="form-check-input" type="checkbox" role="switch"
                           :id="job.id + 'active'" :checked="job.active">
                    <label class="form-check-label" :for="job.id + 'active'">Active</label>
                </div>
                <div>
                    <button v-if="!job.deleted_at" :key="'delete' + job.id" @click="deleteJob(job.id)"
                            class="btn btn-link">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                    <button v-if="job.deleted_at" :key="'restore' + job.id" @click="restoreJob(job.id)"
                            class="btn btn-link">
                        <i class="fas fa-recycle text-success"></i>
                    </button>
                </div>
            </div>
        </article>
        <div v-if="totalPages > 1" class="d-flex align-items-center ms-auto btn-group">
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
    emits: ['addJob', 'editJob'],
    data() {
        return {
            auth_user_id: null,
            loading: true,
            jobsList: [],
            currentPage: 1,
            itemsPerPage: 12,
        }
    },
    computed: {
        paginatedData() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.jobsList.slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.jobsList.length / this.itemsPerPage);
        },
        admin() {
            return localStorage.admin === 'true';
        }
    },
    methods: {
        fetchJobs() {
            return axios.get('/api/jobs/all', {
                headers: {
                    Authorization: "Bearer " + localStorage.remember_token
                },
            }).then(r => {
                this.jobsList = r.data.jobs;
            }).catch(e => {
                console.error(e.response.data.message);
            }).finally(() => {
                this.loading = false;
            })
        },
        async toggleActive(id) {
            try {
                const response = await axios.post('/api/jobs/activate', {
                    id: id,
                }, {
                    headers: {
                        Authorization: "Bearer " + localStorage.remember_token,
                    }
                })
                if (response.data.status === "success") {
                    this.fetchJobs();
                }
            } catch (e) {
                console.error(e.response.data.message);
            }
        },
        async deleteJob(id) {
            try {
                const response = await axios.post('/api/jobs/delete', {
                    id: id,
                }, {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.remember_token,
                    },
                })
                if (response.data.status === "success") {
                    this.fetchJobs();
                }
            } catch (e) {
                console.error(e.response.data.message);
            }
        },
        async restoreJob(id) {
            try {
                const response = await axios.post('/api/jobs/restore', {
                    id: id,
                }, {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.remember_token,
                    },
                })
                if (response.data.status === "success") {
                    this.fetchJobs();
                }
            } catch (e) {
                console.error(e.response.data.message);
            }
        }
    },
    async mounted() {
        await this.fetchJobs();
        console.log(this.jobsList);
        this.auth_user_id = parseInt(localStorage.user_id);
    },
}
</script>
