<template>
  <form>
    <div class="inputs-contain">
      <slot name="inputs"></slot>
    </div>
    <div class="buttons-contain">
      <slot name="buttons"></slot>
    </div>
  </form>
</template>

<script>
export default {
  name: 'Form',
  data () {
    return {
    }
  },
  methods: {
  }
}
</script>

<style lang="scss" scoped>
form {
  .inputs-contain {
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
    :deep(input) {
      margin-bottom: 0.5rem;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 0.25rem;
    }
  }
}
</style>