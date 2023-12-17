function footnotesDesign() {

  function smallScreen(refs, notesWrap, notes) {

    if (window.innerWidth <= 800) { 

      notesWrap.classList.add('hidden')

      if (notes.length > 0) {
        notes.map(note => {
          note.style.display = 'none' 
        })
      }

      if (refs.length > 0) {
        refs.map((ref, idx) => {

          // on click display connected note
          ref.addEventListener('click', (e) => {
            e.preventDefault()

            const id = ref.id.split('-').pop()
            const note = notes.find(note => note.id.split('-').pop() === id)

            // start counting from current li-item, as it is the only one visible
            // and therefore it would otherwise always start from 1
            // note.parentElement.setAttribute('start', idx +1)
            // note.style.setProperty('--footnote-count', idx +1)

            if (note.style.display === 'table-row') {

              note.style.display = 'none'
              notesWrap.removeAttribute('style')

            } else {

              // -- reset
              notes.map(note => {
                note.style.display = 'none' 
              })
              // --
              
              note.style.display = 'table-row'

              notesWrap.style.display = 'block'
              notesWrap.style.position = 'fixed'
              notesWrap.style.bottom = 0
              notesWrap.style.left = 0
              notesWrap.style.backgroundColor = 'white'

            }

          })
        })
      }

    } else {
      bigScreen()
    }
  }

  function bigScreen() {
    if (window.innerWidth >= 800) { 

      // -- reset from small-screen when window-resizing
      notesWrap.classList.remove('hidden')
      notesWrap.removeAttribute('style')

      if (notes.length > 0) {
        notes.map(note => {
          note.removeAttribute('style')
        })
      }
    }
  }

  function solveFor(el, currentTop, parentTop) {
    // get footnote-ref top and handle ref overlapping (eg at the same top pos)

    const previousEl = el.previousElementSibling
    const previousElTop = parseInt(previousEl.style.top, 10)
    const previousElBottom = previousEl.getBoundingClientRect().bottom - parentTop

    return currentTop
  }


  // -- footnotes init
  const refs = Array.from(document.querySelectorAll('.ref-ft'))
  const notesWrap = document.querySelector('.notes')
  const notes = Array.from(notesWrap.querySelectorAll('.notes li'))

  smallScreen(refs, notesWrap, notes)
  bigScreen(refs, notesWrap, notes)

  window.addEventListener('resize', () => {
    smallScreen(refs, notesWrap, notes)
    bigScreen(refs, notesWrap, notes)
  })
  
}

module.exports = footnotesDesign
