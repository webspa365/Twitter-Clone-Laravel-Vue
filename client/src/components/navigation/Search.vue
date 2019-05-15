<template>
  <div class='search'>
    <input type='text' class='form-control' />
    <i class='fa fa-search'></i>
  </div>
</template>

<script>
require('./Search.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'Search',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function(e) {
      var comp = this;
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
