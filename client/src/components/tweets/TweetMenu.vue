<template>
  <div class='tweetMenu'>
    <div><div></div></div>
    <ul>
      <li class='menuItem'>Pin to your profile page</li>
      <li class='menuItem'>Report Tweet</li>
      <li class='menuItem' v-if='this.tweet.username == this.$store.state.account.username'
      @click='open_delete_dialog()'>Delete Tweet</li>
    </ul>
  </div>
</template>

<script>
require('./TweetMenu.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'TweetMenu',
  props: ['tweet'],
  data: function () {
    return {
      msg: '',
    }
  },
  methods: {
    open_delete_dialog: function() {
      this.$store.commit('deleteDialog', this.tweet);
      _('.deleteDialog').style.display = 'block';
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
