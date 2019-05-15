<template>
  <div class='menu'>
    <div><div></div></div>
    <ul>
      <li class='user' @click='to_profile()'>
        <router-link :to="'/profile?user='+this.account.username">
          <span>{{account.name}}</span>
          <span>@{{this.getUsername}}</span>
        </router-link>
      </li>
      <li class='following'>
        <router-link :to="'/profile/following?user='+this.account.username">
          {{account.following}} Following
        </router-link>
      </li>
      <li class='followers'>
        <router-link :to="'/profile/followers?user='+this.account.username">
          {{account.followers}} Followers
        </router-link>
      </li>
      <li class='likes'>
        <router-link :to="'/profile/likes?user='+this.account.username">
          {{account.likes}} Likes
        </router-link>
      </li>
      <li class='logout' @click='log_out()'>Log out</li>
    </ul>
  </div>
</template>

<script>
require('./Menu.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'Menu',
  data: function () {
    return {
      msg: '',
      account: {}
    }
  },
  methods: {
    to_profile: function() {
    },

    log_out: function() {
      this.$store.commit('loggedIn', false);
      this.$store.commit('account', {});
      this.$store.commit('navIcon', '');
      this.$router.push('/');
    }
  },
  computed: {
    getUsername() {
      return this.$store.state.account.username;
    }
  },
  mounted() {
    this.account = this.$store.state.account;
  },
  beforeUpdate() {
    this.account = this.$store.state.account;
  },
  beforeDestroy() {

  }
}
</script>
