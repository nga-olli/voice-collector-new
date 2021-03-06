export const state = () => ({
  data: [],
  query: {},
  formSource: {},
  attrs: {},
  totalItems: 0,
  recordPerPage: 0
});

export const mutations = {
  SET_DATA(state, response) {
    state.data = response.data || null;
    state.totalItems =
      typeof response.meta !== "undefined" ? response.meta.totalItems : 0;
    state.recordPerPage =
      typeof response.meta !== "undefined" ? response.meta.recordPerPage : 0;
  },

  SET_QUERY(state, response) {
    state.query.orderby =
      typeof response.meta !== "undefined"
        ? response.meta.orderBy.toLowerCase()
        : "id";
    state.query.ordertype =
      typeof response.meta !== "undefined"
        ? response.meta.orderType.toLowerCase()
        : "desc";
    state.query.page =
      typeof response.meta !== "undefined" ? response.meta.page : 1;
  },

  SET_FORM_SOURCE(state, response) {
    state.formSource = response.data || null;
  },

  SET_ATTRS(state, response) {
    state.attrs = response.data || null;
  },

  UPDATE_DATA(state, editedItem) {
    const index = state.data.findIndex(item => item.id === editedItem.id);
    state.data.splice(index, 1, editedItem);
  },

  DELETE_DATA(state, removedItem) {
    const index = state.data.findIndex(item => item.id === removedItem.id);
    state.data.splice(index, 1);
    state.totalItems = state.totalItems - 1;
  }
};

export const actions = {
  async get_all({ commit }, { query }) {
    return await this.$axios
      .$get(`/v1/rewardtypes`, {
        params: query
      })
      .then(res => {
        commit("SET_DATA", res);
        commit("SET_QUERY", res);
      });
  },

  async get_form_source({ commit }) {
    return await this.$axios
      .$get(`/v1/rewardtypes/formsource`)
      .then(res => commit("SET_FORM_SOURCE", res));
  },

  async add({ commit }, { formData, covers }) {
    let data = new FormData();

    data.append('form', JSON.stringify(formData));
    covers.map((item, index) => {
      data.append(`files[${index}][value]`, item.raw);
    })
    await this.$axios.$post(`/v1/rewardtypes`, data);
  },

  async get({ commit }, { id }) {
    return await this.$axios.$get(`/v1/rewardtypes/${id}`);
  },

  async update({ commit }, { id, formData, covers }) {
    let data = new FormData();

    data.append('form', JSON.stringify(formData));
    covers.map((item, index) => {
      data.append(`files[${index}][value]`, item.raw);
    })

    return await this.$axios
      .$post(`/v1/rewardtypes/${id}`, data)
      .then(res => commit("UPDATE_DATA", res.data));
  },

  async delete({ commit }, { id }) {
    return await this.$axios
      .$delete(`/v1/rewardtypes/${id}`)
      .then(res => commit("DELETE_DATA", res.data));
  },

  async get_attrs({ commit }, { id }) {
    return await this.$axios
      .$get(`/v1/rewardtypes/${id}/attrs`)
      .then(res => commit("SET_ATTRS", res));
  },

  async update_field({ commit }, formData) {
    await this.$axios.$put(`/v1/rewardtypes/${formData.id}/field`, formData);
  },

  async change_status({ commit }, formData) {
    await this.$axios.$put(`/v1/rewardtypes/${formData.id}/status`, formData);
  },

  async update_attr_field({ commit }, formData) {
    await this.$axios.$put(
      `/v1/rewardtypes/${formData.id}/attr_field`,
      formData
    );
  },

  async delete_attr({ commit }, { id }) {
    return await this.$axios.$delete(`/v1/rewardtypes/${id}/attr`);
  }
};
