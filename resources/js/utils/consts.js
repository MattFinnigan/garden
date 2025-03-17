
const defaultCrop = (plant = null) => {
  return {
    plant_id: plant ? plant.id : null,
    crop_entries: [],
    spacing_x: 1,
    spacing_y: 1,
    height: 1,
    width: 1,
    qty: 1
  }
}

const defaultCropEntry = (bed = null, crop = null) => {
  return {
    action: 'Planned',
    stage: 'Planned',
    notes: null,
    images: [],
    datetimestamp: new Date().toISOString().slice(0, 16),
    crop_id: crop ? crop.id : null
  }
}

export {
  defaultCrop,
  defaultCropEntry
}