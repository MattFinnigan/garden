<template>
  <div>
    <nav>
      <MainMenu/>
    </nav>
    <main>
      <RouterView/>
    </main>
  </div>
</template>

<script>
import MainMenu from './layout/MainMenu.vue'
export default {
  components: {
    MainMenu
  },
  data () {
    return {
      message: 'Welcome to my Vue app!'
    }
  },
  methods: {
    greet() {
      console.log(this.message)
    }
  },
  mounted() {
    this.greet()
  }
}
</script>
