
<template>
  <div class="main-menu">
    <div class="container">
      <div>Garden Planner & Journal</div>
      <div><Button class="sm icon primary" @click="showTodoModal = !showTodoModal"><Icon name="info"/></Button></div>
      <Modal v-if="showTodoModal" cancelText="Close" @close="showTodoModal = false">
        <template #header>
          <h3>Todo</h3>
        </template>
        <template #content>
          <ul>
            <li>Improve mobile/tablet support</li>
            <li>Check userflow for potential improvements (Plant create > crop create > entry create, etc.)</li>
            <li>Entity resizing available from all four corners</li>
            <li>Keyboard shortcuts</li>
            <li>Better way to exit 'Create' modes</li>
            <li>Handle crops that grow through to the new year (+Year select)</li>
            <li>Unused code clean up</li>
            <li>Small CSS adjustments (Button icons)</li>
          </ul>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script>

export default {
  data () {
    return {
      showTodoModal: false
    }
  }
}
</script>
<style lang="scss" scoped>
.main-menu {
  border-bottom: 1px solid $borderColour;
  .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1em;
    height: 20px;
  }
}
</style>
