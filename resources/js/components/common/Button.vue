<template>
  <button :class="classes" :type="type" :disabled="disabled">
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
    classes: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  name: 'Button'
}
</script>

<style scoped lang="scss">
button {
  padding: 0.5rem 1.75rem;
  border-radius: 0.5rem;
  font-size: $fsBody;
  transition: all 0.25s;
  border: none;
  cursor: pointer;
  &.link {
    margin: 0;
    padding: 0;
    background-color: transparent;
    border: none !important;
    outline: none;
    &:hover {
      text-decoration: underline;
      background-color: transparent !important;
      &.primary {
        color: $primary;
      }
    }
  }
  &.primary {
    background-color: $primary;
    color: $backgroundColour;
    &.outline {
      background-color: transparent;
      border: 1px solid $primary;
      color: $primary;
    }
    &:not(.outline):hover {
      background-color: $primary;
      color: $backgroundColour;
    }
  }
  &.secondary {
    background-color: $secondary;
    color: $backgroundColour;
    &.outline {
      background-color: transparent;
      border: 1px solid $secondary;
      color: $secondary;
      &:hover {
        background-color: $secondary;
        color: $backgroundColour;
      }
    }
  }
  &.secondary2 {
    background-color: $secondary2;
    color: $secondary;
    &.outline {
      background-color: transparent;
      border: 1px solid $secondary2;
      color: $secondary2;
      &:hover {
        background-color: $secondary2;
        color: $secondary;
      }
    }
  }
  &.danger {
    background-color: $danger;
    color: $backgroundColour;
    &.outline {
      background-color: $backgroundColour;
      border: 1px solid $danger;
      color: $danger;
      &:hover {
        background-color: $danger;
        color: $backgroundColour;
        :deep(i) {
          background-color: $backgroundColour;
        }
      }
    }
  }
  &.sm {
    font-size: $fsSmall;
    padding: 0.5rem 1.5rem;
  }
  &.icon {
    padding: 0.4em 0.785em;
    border-radius: 0.625rem;
    &.sm {
      padding: 0.25em 0.5em;
    }
  }
  &.rounded {
    border-radius: 1.25rem;
  }
}
// button {
//   margin-right: 0.5em;
//   padding: 0.5rem 1rem;
//   border: none;
//   border-radius: 0.25rem;
//   background-color: $primary;
//   color: white;
//   font-size: $fsBody;
//   cursor: pointer;
//   &:hover {
//     background-color: $primary;
//   }
//   &.link {
//     margin: 0;
//     padding: 0;
//     background-color: transparent;
//     color: $primary;
//     &:hover {
//       text-decoration: underline;
//     }
//   }
//   &.bare {
//     margin: 0;
//     padding: 0;
//     border-radius: unset;
//     background-color: transparent;
//     &:hover {
//       text-decoration: none;
//     }
//   }
//   &.sm {
//     padding: 0.25rem 0.5rem;
//     font-size: $fsSmall;
//   }
//   &[disabled] {
//     opacity: 0.8;
//     background-color: $grey-700;
//     cursor: not-allowed;
//   }
//   i {
//     display: inline-block;
//     width: 1.1em;
//     height: 1.1em;
//     background-color: white;
//     mask-repeat: no-repeat;
//     vertical-align: middle;
//     text-align: center;
//     mask-size: 13px 13px;
//     &.edit {
//       mask-image: url('/images/edit.svg');
//     }
//     &.delete {
//       mask-image: url('/images/delete.svg');
//     }
//     &.view {
//       mask-image: url('/images/view.svg');
//     }
//   }
// }
</style>