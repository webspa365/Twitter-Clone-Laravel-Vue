<template>
  <div class='replyDialog' @click='close_dialog($event)'>
    <div class='wrapper modal-content'>
      <div class='modal-header'>
        <h3>Reply to {{replyTo.name}}@{{replyTo.username}}</h3>
        <i class='fa fa-times closeButton' @click='close_dialog($event)'></i>
      </div>
      <div class='replyTo'>
        <Tweet v-if='this.$store.state.replyTo' :data='replyTo' />
      </div>
      <div class='modal-body'>
        <div class='replying'>
          <span>Replying to @{{replyTo.username}}</span>
        </div>
        <div class='avatar'>
          <i v-if='avatar === ""' class='fa fa-user'></i>
          <img v-else src='this.$store.state.account.avatar' />
        </div>
        <div class='textarea'>
          <textarea class='form-control' type='text' placeholder="Tweet your reply" v-model='reply' />
        </div>
      </div>
      <div class='modal-footer'>
        {{msg}}
        <div>
          <ul class='icons'>
            <li><i class='fa fa-image'></i></li>
            <li><i class='fa fa-camera'></i></li>
            <li><i class='fa fa-map-o'></i></li>
            <li><i class='fa fa-map-marker'></i></li>
          </ul>
          <button :class="'btn btn-default addButton '+active"><i class='fa fa-plus'></i></button>
          <button :class="'btn btn-primary replyButton '+active" @click='post_reply()'>Reply</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
require('./ReplyDialog.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Tweet from '@/components/tweets/Tweet'

export default {
  name: 'ReplyDialog',
  components: {
    Tweet
  },
  data: function() {
    return {
      msg: '',
      toName: '',
      avatar: '',
      replyTo: {name: 'name', username: 'username'},
      reply: '',
      active: ''
    }
  },
  methods: {
    close_dialog: function(e) {
      var classList = e.target.classList.toString();
      if(classList.indexOf('replyDialog') > -1 || classList.indexOf('closeButton') > -1) {
        this.msg = '';
        this.reply = '';
        this.$store.commit('replyTo', '');
        _('.replyDialog').style.display = 'none';

      }
    },
    post_reply: function() {
      console.log('post_reply()');
      if (localStorage.getItem("jwtToken") === null || !this.$store.state.loggedIn) {
        this.$router.push('/login');
        return;
      }
      if(!this.reply) return;
      if(!this.$store.state.replyTo) return;
      _('.loader').style.display = 'block';
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/replies', {
        tweet: this.reply,
        toId: this.replyTo.id,
        toUsername: this.replyTo.username
      }).then((res) => {
        console.log('/api/replies='+JSON.stringify(res.data));
        _('.loader').style.display = 'none';
        if(res.data.success) {
          // update tweets
          var reply = res.data.reply;
          var tweets = this.$store.state.tweets;
          // add count replies
          for(var t of tweets) {
            if(t.id === this.replyTo.id) {
              t.replies++;
            }
          }
          // add new tweet(reply)
          tweets.unshift(reply);
          this.$store.commit('tweets', tweets);
          setTimeout(() => {
            this.$root.$emit('updateTweets');
          }, 100);

          // update count tweets at profile and accounts
          var count = res.data.count;
          if(reply.userId == this.$store.state.profile.id) {
            var profile = this.$store.state.profile;
            profile.tweets = count;
            this.$store.commit('profile', profile);
          }
          if(reply.userId == this.$store.state.account.id) {
            var account = this.$store.state.account;
            account.tweets = count;
            this.$store.commit('account', account);
          }

          // close dialog
          this.msg = '';
          this.reply = '';
          this.$store.commit('replyTo', '');
          _('.replyDialog').style.display = 'none';
        } else {
          this.msg = res.data.msg;
        }
      }).catch((err) => {
        console.log(err)
        _('.loader').style.display = 'none';
      });
    }
  },

  beforeUpdate() {
    console.log('beforeUpdate()');
    if(this.$store.state.replyTo) {
      this.replyTo = this.$store.state.replyTo;
    }
  },

  updated() {
    console.log('updated()');
    if(this.reply.length > 0) this.active = 'active';
    else this.active = '';
  },

  mounted() {
    this.$store.commit('replyTo', '');
  },

  beforeDestroy() {

  }
}
</script>
