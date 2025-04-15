import { defineStore } from 'pinia';
import axios from 'axios';

export const useDataStore = defineStore('data', {
  state: () => ({
    items: [],
    stats: {
      users: 0,
      projects: 0,
      requests: 0
    },
    loading: false,
    error: null,
    pagination: {
      currentPage: 1,
      totalPages: 1,
      perPage: 10
    }
  }),

  getters: {
    hasItems: (state) => state.items.length > 0,
    totalItems: (state) => state.items.length
  },

  actions: {
    async fetchData(page = 1) {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('/api/data', {
          params: { page, per_page: this.pagination.perPage }
        });

        this.items = response.data.data;
        this.pagination = {
          currentPage: response.data.current_page,
          totalPages: response.data.last_page,
          perPage: response.data.per_page
        };

        return this.items;
      } catch (error) {
        this.error = 'Ошибка при получении данных';
        console.error('Fetch data error:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },

    async fetchStats() {
      try {
        const response = await axios.get('/api/stats');
        this.stats = response.data;
        return this.stats;
      } catch (error) {
        console.error('Fetch stats error:', error);
        return this.stats;
      }
    }
  }
});
