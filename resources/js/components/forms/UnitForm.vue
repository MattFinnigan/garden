<template>
  <div class="unit-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h2>Unit Form</h2>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <Input type="text" v-model="currUnit.name" label="Name" required minlength="2" maxlength="255"/>
          <Input :modelValue="currUnit.image" type="file" label="Image" @change="e => currUnit.image = e.target.value"/>
        </template>
        <template #buttons>
          <Button type="submit" :disabled="loading">Submit</Button>
        </template>
      </Form>
    </template>
  </Modal>
</div>
</template>

<script>
import { createUnit, updateUnit } from '../../utils/api'
import { isEmpty } from '../../utils/helpers';
export default {
  name: 'UnitForm',
  props: {
    val: Object
  },
  emits: ['add', 'patch', 'close'],
  data () {
    return {
      currUnit: isEmpty(this.val) || this.val ? this.val : {
        name: '',
        image: null,
        action: 'Sowed',
        stage: 'Planned',
        area: 1
      },
      editAction: false,
      editStage: false,
      editArea: false,
      loading: false
    }
  },
  methods: {
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.currUnit.id) {
        updateUnit(this, this.currUnit.id, this.currUnit).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else {
        createUnit(this, this.currUnit).then(response => {
          this.$emit('add', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      }
    }
  }
}
</script>

<style scoped lang="scss">
h2 {
  margin: 0;
}
</style>