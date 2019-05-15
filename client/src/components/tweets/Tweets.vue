<template>
  <div class='tweets'>
    <div class='header'>
      <ul class='tweets_ul'>
        <li class='li_tweets active' @click='get_tweets()'>
          <router-link :to="'/profile?user='+this.username">Tweets</router-link>
        </li>
        <li class='li_replies' @click='get_replies()'>
          <router-link :to="'/profile/replies?user='+this.username">Tweets & replies</router-link>
        </li>
        <li class='li_media' @click='get_media()'>
          <router-link :to="'/profile/media?user='+this.username">Media</router-link>
        </li>
      </ul>
    </div>
    <div class='body'>
      <ul>
        {{'this.skip='+this.skip}}
        <li v-for='(tweet, index) in tweets' :key='index'>
          <Tweet :data='tweet' />
        </li>
      </ul>
    </div>
    <div v-if='true' class='footer' @click='show_more()'>
      <span>Show more...</span>
    </div>
    <div else='' class='footer' @click='back_to_top()'>
      <span>Back to Top</span>
    </div>
  </div>
</template>

<script>
require('./Tweets.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Tweet from '@/components/tweets/Tweet'

export default {
  name: 'Tweets',
  components: {
    Tweet
  },
  props: ['skip', 'get_tweets', 'type', 'get_likes'],
  data: function () {
    return {
      msg: '',
      username: '',
      tweets: []
    }
  },
  computed: {
    setTweets: function() {
      this.tweets = this.$store.state.tweets;
      return this.tweets;
    },
    getTweets: function() {
      return this.$store.state.tweets;
    }
  },
  methods: {
    /*
    get_tweets: function() {
      console.log('get_tweets()');

    },
    */
    get_replies: function() {

    },

    get_media: function() {

    },

    show_more: function() {
      console.log('show_more');
      if(this.type === 'tweets') {
        this.get_tweets();
      } else {
        this.get_likes();
      }

    },

    back_to_top: function() {

    }
  },

  created() {
    this.$root.$on('updateTweets', () => {
      console.log('$on("updateTweets")');
      this.tweets = [];
      setTimeout(() => {
        this.tweets = this.$store.state.tweets;
      }, 100);

      //this.setTweets;
    });
  },

  mounted() {
    //this.get_tweets();
  },

  beforeDestroy() {

  }
}
</script>
