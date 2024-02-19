module.exports = {
  init() {
    const galleries = document.querySelectorAll( '.block.gallery' )

    for ( const gallery of galleries ) {
      const list = gallery.querySelector( 'ol' )
      const left = gallery.querySelector( '.left' )
      const right = gallery.querySelector( '.right' )
      const rem = parseFloat(getComputedStyle(document.documentElement).fontSize)

      right.onclick = e => {
        e.preventDefault()
        list.scroll({
          left: list.scrollLeft + gallery.offsetWidth - 2 * rem,
          behavior: "smooth"
        })
      }

      left.onclick = e => {
        e.preventDefault()
        list.scroll({
          left: list.scrollLeft - gallery.offsetWidth + 2 * rem,
          behavior: "smooth"
        })
      }

    }

  }
}
