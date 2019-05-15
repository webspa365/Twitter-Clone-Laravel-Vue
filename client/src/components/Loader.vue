<template>
  <div class='loader'>
    <div class='spinner_wrapper'>
      <div class='spinner'></div>
    </div>
  </div>
</template>

<script>
require('./Loader.css')

export default {
  name: 'Loader',
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
