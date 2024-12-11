<template>
  <router-link :class="classes" :to="to">
    <slot></slot>
  </router-link>
</template>

<script>
export default {
  props: {
    to: {},
    classes: {
      type: String,
      default: ''
    }
  },
  name: 'Link',
}
</script>

<style scoped lang="scss">
a {
  margin: 0;
  padding: 0;
  background-color: transparent;
  color: $primary;
  &:hover {
    text-decoration: underline;
  }
  &.button {
    margin-right: 0.5em;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    background-color: $primary;
    color: white;
    font-size: $fsBody;
    cursor: pointer;
    &:hover {
      background-color: $primary;
    }
    &.link {
      margin: 0;
      padding: 0;
      background-color: transparent;
      color: $primary;
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
      font-size: $fsSmall;
    }
    &[disabled] {
      opacity: 0.8;
      background-color: $grey-700;
      cursor: not-allowed;
    }
  }
}
</style>