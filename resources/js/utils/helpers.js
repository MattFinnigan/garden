
export const clone = (obj) => {
  return JSON.parse(JSON.stringify(obj))
}

export const isEmpty = (obj) => {
  return Object.keys(obj).length === 0
}

export const isOverlapping = (el1, el2) => {
  const rect1 = el1.getBoundingClientRect()
  const rect2 = el2.getBoundingClientRect()
  return !(
    rect1.right < rect2.left ||
    rect1.left > rect2.right ||
    rect1.bottom < rect2.top ||
    rect1.top > rect2.bottom
  )
}
export const arrangePlantsInBedWithOverlapCheck = (lastEntry, bed) => {
  const res = []
  const bedEl = document.createElement('div')
  bedEl.classList.add('bed-container-poscheck')
  bedEl.style.width = `${bed.l}px`
  bedEl.style.height = `${bed.w}px`
  bedEl.style.position = 'relative'
  bedEl.style.backgroundColor = 'black'
  document.body.appendChild(bedEl)

  let x = 0
  let y = 0
  const step = 5 // Increment step for x and y when moving to find space

  for (let i = 0; i < lastEntry.qty; i++) {
    const plantEl = document.createElement('div')
    plantEl.classList.add('bed-plant-poscheck')
    plantEl.style.padding = `${lastEntry.area / 2}px`
    plantEl.style.position = 'absolute'
    plantEl.style.backgroundColor = 'green'

    // Find a position that does not overlap
    let placed = false
    while (!placed) {
      plantEl.style.left = `${x}px`
      plantEl.style.top = `${y}px`
      bedEl.appendChild(plantEl) // Temporarily append to check overlap

      const isOverlappingAny = Array.from(bedEl.children).some((existingPlant) => {
        return existingPlant !== plantEl && isOverlapping(plantEl, existingPlant)
      })

      if (isOverlappingAny) {
        bedEl.removeChild(plantEl) // Remove to retry placement
        x += step
        if (x + lastEntry.area >= bed.l) {
          x = 0
          y += step
        }
        if (y + (lastEntry.area / 2) > bed.w) {
          console.warn("Not enough space to place all plants.")
          return res // Stop placement if no space is left
        }
      } else {
        placed = true
      }
    }

    res.push({ x, y })
  }

  return res
}

export const draggable = (el, relativeEl, start, update, end, padding = 0) => {
  let isDragging = false
  let clickOffsetX = 0
  let clickOffsetY = 0

  const getZoomFactor = () => {
    const computedStyle = window.getComputedStyle(relativeEl)
    return parseFloat(computedStyle.zoom) || 1
  }

  const onMouseMove = (e) => {
    if (!isDragging) {
      return
    }

    const parentRect = relativeEl.getBoundingClientRect()
    const zoomFactor = getZoomFactor()

    // Calculate the new position, adjusting for zoom and offsets
    let x = ((e.clientX - parentRect.left) / zoomFactor) - clickOffsetX + relativeEl.scrollLeft
    let y = ((e.clientY - parentRect.top) / zoomFactor) - clickOffsetY + relativeEl.scrollTop

    // Constrain to parent's top and left boundaries only
    x = Math.max(0, x)
    y = Math.max(0, y)

    update({ x, y })
  }

  const onMouseUp = (e) => {
    if (!isDragging) {
      return
    }

    isDragging = false
    document.removeEventListener('mousemove', onMouseMove)
    document.removeEventListener('mouseup', onMouseUp)

    const parentRect = relativeEl.getBoundingClientRect()
    const zoomFactor = getZoomFactor()

    let x = ((e.clientX - parentRect.left) / zoomFactor) - clickOffsetX + relativeEl.scrollLeft
    let y = ((e.clientY - parentRect.top) / zoomFactor) - clickOffsetY + relativeEl.scrollTop

    // Constrain to parent's top and left boundaries only
    x = Math.max(0, x)
    y = Math.max(0, y)

    end({ x, y })
  }

  el.addEventListener('mousedown', (e) => {
    if (e.target !== el) {
      return
    }

    if (padding) {
      const rect = el.getBoundingClientRect()
      if (
        e.clientX < rect.left + padding ||
        e.clientX > rect.right - padding ||
        e.clientY < rect.top + padding ||
        e.clientY > rect.bottom - padding
      ) {
        return
      }
    }

    const elRect = el.getBoundingClientRect()
    const parentRect = relativeEl.getBoundingClientRect()
    const zoomFactor = getZoomFactor()

    // Record the offset where the mouse clicked within the element
    clickOffsetX = (e.clientX - elRect.left) / zoomFactor
    clickOffsetY = (e.clientY - elRect.top) / zoomFactor

    isDragging = true

    const startX = (elRect.left - parentRect.left + relativeEl.scrollLeft) / zoomFactor
    const startY = (elRect.top - parentRect.top + relativeEl.scrollTop) / zoomFactor

    start({ x: startX, y: startY })

    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)
  })
}



