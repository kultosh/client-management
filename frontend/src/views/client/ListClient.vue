<template>
  <div>
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex flex-row bd-highlight mb-3">
          <input type="text" class="form-control me-2" placeholder="Search">
          <select class="form-select w-auto" aria-label="View">
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
        <DataTable :clients="clients" :pagination="pagination" />
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from "../../components/DataTable.vue";

export default {
    components: {
        DataTable,
    },
    data() {
        return {
            clients: [
                {
                    id: 1,
                    company_name: 'Apple',
                    email: "apple@test.com",
                    phone_number: "+977 987676673"
                },
                {
                    id: 2,
                    company_name: 'Microsoft',
                    email: "microsoft@test.com",
                    phone_number: "+977 936676673"
                },
                {
                    id: 3,
                    company_name: 'Samsung',
                    email: "samsung@test.com",
                    phone_number: "+977 958976673"
                },
                {
                    id: 4,
                    company_name: 'Meta',
                    email: "meta@test.com",
                    phone_number: "+977 931606673"
                }
            ],
            pagination: {
              current_page: 1,
              last_page: 5
            },
        };
    },
};
</script>

<style scoped>
.card-header {
  background-color: #f8f9fa;
}
</style>