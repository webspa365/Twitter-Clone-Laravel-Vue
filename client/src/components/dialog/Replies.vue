<template>
  <div class='replies' @click='close_replies($event)'>
    <div class='wrapper'>
      <div class='replied'>
        <Tweet v-if='this.$store.state.replied' :data='this.$store.state.replied' />
        <div v-else class='notFound' style='color: #888'>{{this.msg}}</div>
      </div>
      <div class='header'>
        <div class='input'>
          <input class='form-control' placeholder="Tweet your reply" />
          <i class='fa fa-image'></i>
        </div>
        <div class='avatar'>
          <img v-if='this.$store.state.account.avatar' :src='this.$store.state.account.avatarT' />
          <i v-else class='fa fa-user'></i>
        </div>
      </div>
      <div class='body'>
        <ul>
          <li v-for='reply in this.$store.state.replies'><Tweet :data='reply' /></li>
        </ul>
      </div>
      <div class='footer'>
        <span>Show more replies</span>
      </div>
    </div>
  </div>
</template>

<script>
require('./Replies.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Tweet from '@/components/tweets/Tweet'

export default {
  name: 'Replies',
  data: function () {
    return {
      msg: '',
      replied: {},
      replies: {},
      footer: {}
     }
  },
  components: {
    Tweet
  },
  methods: {
    close_replies: function(e) {
      var classList = e.target.classList.toString();
      if(classList.indexOf('replies') > -1 || classList.indexOf('closeButton') > -1) {
        this.msg = '';
        _('.replies').style.display = 'none';
      }
    }
  },

  mounted() {

  },

  beforeDestroy() {

  }
}
</script>
