<template>
  <button :class="styling" :type="type">
    <slot></slot>
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'button'
    },
    styling: {
      type: String,
      default: ''
    }
  },
  name: 'Button',
}
</script>

<style scoped lang="scss">
button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.25rem;
  background-color: blue;
  color: white;
  cursor: pointer;
  &:hover {
    background-color: darkblue;
  }
  &.link {
    padding: 0;
    background-color: transparent;
    color: blue;
    &:hover {
      text-decoration: underline;
    }
  }
  &.bare {
    padding: 0;
    border-radius: unset;
    background-color: transparent;
    color: black;
    &:hover {
      text-decoration: none;
    }
  }
}
</style>