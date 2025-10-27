<template>
  <div>
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex flex-row bd-highlight mb-3">
          <input type="text" class="form-control me-2" placeholder="Search" v-model="searchQuery" @input="handleSearch()">
          <select class="form-select w-auto" aria-label="View" v-model="selectedView" @change="handleViewChange()">
            <option selected disabled>View</option>
            <option value="all">All</option>
            <option value="unique">Unique</option>
            <option value="duplicate">Duplicate</option>
          </select>
        </div>
        <div class="d-flex flex-row bd-highlight mb-3">
          <button class="btn btn-outline-success me-2">Import</button>
          <select class="form-select" aria-label="Export">
            <option selected disabled>Export</option>
            <option value="all">All</option>
            <option value="unique">Unique</option>
            <option value="duplicate">Duplicate</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <DataTable :clients="clients" :pagination="pagination" @page-changed="fetchClients" />
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from "../../components/DataTable.vue";
import { getClients } from '@/services/client';

export default {
  components: {
      DataTable,
  },
  data() {
    return {
      clients: [],
      pagination: {
        current_page: 1,
        last_page: 1
      },
      selectedView: 'all',
      searchQuery: '',
      isLoading: false
    };
  },
  created() {
    this.fetchClients();
  },
  methods: {
    fetchClients(page = 1) {
      this.isLoading = true;

      const params = {
        page,
        filter: this.selectedView,
        search: this.searchQuery || undefined
      };

      Object.keys(params).forEach(key => {
        if (params[key] === undefined) {
          delete params[key];
        }
      });

      getClients(params)
        .then((response) => {
          const data = response.data;

          if (data.code == 200) {
            this.clients = data.content.data;
            this.pagination = {
              current_page: data.content.current_page,
              last_page: data.content.last_page,
              total: data.content.total,
              per_page: data.content.per_page,
            };
          }
        })
        .catch(err => {
          console.error('Failed To List Clients:', err);
          this.clients = [];
          this.pagination = {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 10,
          };
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    handleViewChange() {
      this.pagination.current_page = 1;
      this.fetchClients(1);
    },
    handleSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1;
        this.fetchClients(1);
      }, 500);
    }
  }
};
</script>

<style scoped>
.card-header {
  background-color: #f8f9fa;
}
</style>