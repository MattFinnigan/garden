<template>
  <button :class="classes" :type="type" :disabled="disabled">
    <slot></slot>
    <i v-if="edit" class="edit"></i>
    <i v-if="view" class="view"></i>
    <i v-if="del" class="delete"></i>
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'button'
    },
    classes: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  name: 'Button',
  computed: {
    edit () {
      return this.$attrs.edit !== undefined
    },
    del () {
      return this.$attrs.del !== undefined
    },
    view () {
      return this.$attrs.view !== undefined
    }
  }
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
  i {
    display: inline-block;
    width: 1.1em;
    height: 1.1em;
    background-color: $white;
    mask-repeat: no-repeat;
    vertical-align: middle;
    text-align: center;
    mask-size: 13px 13px;
    &.edit {
      mask-image: url('/images/edit.svg');
    }
    &.delete {
      mask-image: url('/images/delete.svg');
    }
    &.view {
      mask-image: url('/images/view.svg');
    }
  }
}
</style>