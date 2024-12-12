<template>
  <div class="display-input">
    <label>{{ label }}</label>
    <div class="value">{{ val }}</div>
  </div>
</template>

<script>
export default {
  name: 'Display',
  props: {
    label: {
      type: String,
      required: true
    },
    val: {
      required: true
    }
  }
}
</script>

<style scoped lang="scss">
.display-input {
  display: flex;
  margin-bottom: 1em;
}
</style>