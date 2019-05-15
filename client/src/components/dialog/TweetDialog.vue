<template>
  <div class='tweetDialog' @click='close_dialog($event)'>
    <div class='wrapper modal-content'>
      <div class='modal-header'>
        <h3>Compose new Tweet</h3>
        <i class='fa fa-times closeDialog' @click='close_dialog($event)'></i>
      </div>
      <div class='modal-body'>
        <div class='avatar'>
          <i class='fa fa-user'></i>
          {{avatar}}
        </div>
        <div class='textarea'>
          <textarea class='form-control' type='text' placeholder="What's happening?" v-model='tweet' />
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
          <button :class="'btn btn-primary tweetButton '+active" @click='post_tweet()'>Tweet</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
require('./TweetDialog.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Tweets from '@/components/tweets/Tweets'

export default {
  name: 'TweetDialog',
  data: function () {
    return {
      msg: '',
      avatar: '',
      message: '',
      tweet: '',
      active: ''
    }
  },
  methods: {
    close_dialog: function(e) {
      var classList = e.target.classList.toString();
      if(classList.indexOf('tweetDialog') > -1 || classList.indexOf('closeDialog') > -1) {
        this.tweet = '';
        _('.tweetDialog').style.display = 'none';
      }
    },

    change_text() {

    },

    post_tweet: function() {
      if (localStorage.getItem("jwtToken") === null || !this.$store.state.loggedIn) {
        this.$router.push('/');
        return;
      }
      if(!this.tweet) return;
      _('.loader').style.display = 'block';
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/tweets', {tweet: this.tweet})
      .then((res) => {
        console.log('/api/tweets(post)='+JSON.stringify(res.data));
        _('.loader').style.display = 'none';
        if(res.data.success) {
          if(this.$store.state.profile.username === this.$store.state.account.username) {
            // update profile
            var profile = this.$store.state.profile;
            profile.tweets = res.data.count;
            this.$store.commit('profile', profile);
            // update tweets
            var tweets = this.$store.state.tweets;
            tweets.unshift(res.data.tweet);
            this.$store.commit('tweets', tweets);
            console.log('this.$store.state.tweets='+JSON.stringify(this.$store.state.tweets));
            this.$root.$emit('updateTweets');
            console.log('this.$root.$emit');

            /*
            var t = res.data.tweet;
            t.avatar = this.$store.state.account.avatar;
            var tweets = this.$store.state.tweets;
            if(tweets.length > 0) tweets = [t, ...this.$store.state.tweets];
            else tweets[0] = t;
            this.$store.state.setTweets(tweets);*/
            this.tweet = '';
            _('.tweetDialog').style.display = 'none';
          } else {
            this.tweet = '';
            _('.tweetDialog').style.display = 'none';
            this.$router.push('/profile?user='+this.$store.state.account.username);
          }
        } else {
          this.msg = res.data.msg;
        }
      }).catch((err) => {
        console.log(err)
        _('.loader').style.display = 'none';
      });
    }
  },
  mounted() {

  },

  updated() {
    if(this.tweet.length > 0) this.active = 'active';
    else this.active = '';
  },

  beforeDestroy() {

  }
}
</script>
