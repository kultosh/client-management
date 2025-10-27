<template>
  <div>
    <table class="table table-striped table-bordered" id="pagesTable">
      <thead>
        <tr>
            <th>ID</th>
            <th>Company Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Actions</th>
        </tr>
      </thead>
      <tbody v-if="clients.length">
        <tr v-for="client in clients" :key="client.id">
            <td>{{ client.id }}</td>
            <td>{{ client.company_name }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.phone_number }}</td>
            <td>
            <button type="button" class="btn btn-sm btn-primary me-2">Edit</button>
            <button type="button" class="btn btn-sm btn-danger">Delete</button>
            </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td :colspan="columns.length"><div class="d-flex justify-content-center w-100">No Data</div></td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav v-if="pagination && totalPages > 1">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
        </li>

        <li
          v-for="pageNumber in totalPages"
          :key="pageNumber"
          class="page-item"
          :class="{ active: currentPage === pageNumber }"
        >
          <button class="page-link" @click="changePage(pageNumber)">{{ pageNumber }}</button>
        </li>

        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
export default {
  props: {
    clients: {
      type: Array,
      required: true,
    },
    pagination: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {}
  },
  computed: {
    currentPage() {
      return this.pagination.current_page;
    },
    totalPages() {
      return this.pagination.last_page || 0;
    },
  },
  methods: {
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.$emit("page-changed", page);
      }
    },
  },
};
</script>

<style scoped>
.table {
  width: 100%;
}   
</style>