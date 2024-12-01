<template>
  <button :class="styling" :type="type" :disabled="disabled">
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
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  name: 'Button',
}
</script>

<style scoped lang="scss">
button {
  margin-right: 0.5em;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.25rem;
  background-color: $primary-green;
  color: $white;
  font-size: $fontSize-m;
  cursor: pointer;
  &:hover {
    background-color: $primary-green;
  }
  &.link {
    margin: 0;
    padding: 0;
    background-color: transparent;
    color: $primary-green;
    &:hover {
      text-decoration: underline;
    }
  }
  &.bare {
    margin: 0;
    padding: 0;
    border-radius: unset;
    background-color: transparent;
    &:hover {
      text-decoration: none;
    }
  }
  &.sm {
    padding: 0.25rem 0.5rem;
    font-size: $fontSize-s;
  }
  &[disabled] {
    opacity: 0.8;
    background-color: $grey-700;
    cursor: not-allowed;
  }
}
</style>