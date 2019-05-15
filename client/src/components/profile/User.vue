<template>
  <div class='user'>
    <div class='bg' @click='click_user()'>
      <img :src='user.bg' onerror="this.style.display='none'" />
    </div>
    <div class='avatar' @click='click_user()'>
      <img :src='user.avatar' onerror="this.style.display='none'" />
    </div>
    <div class='info'>
      <h3>
        <span class='name'>{{user.name}}</span>
        <span class='username' @click='click_user()'>@{{user.username}}{{followsYou}}</span>
      </h3>
      <p class='bio'>{{user.bio}}</p>
      <button v-if='user.followed === 0'
      class='btn btn-default followButton' @click='follow_user()'></button>
      <button v-else
      class='btn btn-default followButton followed' @click='follow_user()'></button>
    </div>
    </div>
  </div>
</template>

<script>
require('./User.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'User',
  props: ['data'],
  data: function () {
    return {
      msg: '',
      user: this.data,
      followsYou: ''
    }
  },
  methods: {
    click_user: function() {
      this.$router.push('/profile?user='+this.user.username);
    },

    follow_user: function() {
      console.log('follow_user()');
      if(!this.$store.state.loggedIn || !localStorage.getItem('jwtToken')) {
        this.$router.push('/');
        return;
      }
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/relationships', {userId: this.user.id})
      .then((res) => {
        console.log('/relationships='+JSON.stringify(res.data));
        if(res.data.success) {
          if(res.data.followed) {
            this.user.followed = 1;
          } else {
            this.user.followed = 0;
          }
          this.$store.state.account.following = res.data.following;
          if(this.$store.state.account.id === this.$store.state.profile.id) {
            this.profile.following = res.data.following;
          } else {
            this.profile.followers = res.data.followers;
          }
        }
      });
    }
  },
  mounted() {
    console.log('mounted() '+JSON.stringify(this.data));
  },
  beforeDestroy() {

  }
}
</script>
