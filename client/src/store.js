import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'

var key = 'vue-twitter';

Vue.use(Vuex);

export const store = new Vuex.Store({
  getters: {},
  actions: {},
  mutations: {
    loggedIn(state, payload) {
      state.loggedIn = payload;
    },
    account(state, payload) {
      state.account = payload;
    },
    profile(state, payload) {
      state.profile = payload;
    },
    users(state, payload) {
      state.users = payload;
    },
    tweets(state, payload) {
      state.tweets = payload;
    },
    replyTo(state, payload) {
      state.replyTo = payload;
    },
    replied(state, payload) {
      state.replied = payload;
    },
    replies(state, payload) {
      state.replies = payload;
    },
    navIcon(state, payload) {
      state.navIcon = payload;
    },
    deleteDialog(state, payload) {
      state.deleteDialog = payload;
    },
    loading(state, payload) {
      state.loading = payload;
    }
  },
  state: {
    dialog: {},
    loggedIn: false,
    account: {},
    profile: {},
    users: [],
    tweets: [],
    replyTo: {},
    replied: {},
    replies: [],
    navIcon: '',
    deleteDialog: {},
    loading: false
  },
  plugins: [createPersistedState({
    key: key,
    storage: window.localStorage
  })]
});
