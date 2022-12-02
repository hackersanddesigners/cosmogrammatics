export default class SelectionObserver {

  HIGHLIGHT_CLASS = "highlight"
  _SELECTION = null
  wrappers = []
  rangeOffset = []
  dragging = false

  constructor( target, toolbar ) {
    for ( const event of [
      'mousedown',
      'mousemove',
      'mouseup',
      'keyup'
    ]) {
      target.addEventListener( event, e => { this[ `${ event }_handler` ]( e ) } )
    }
    this.target  = target
    this.toolbar = toolbar
  }

  mousedown_handler( e ) {
    this.dragging = true
  }
  mousemove_handler( e ) {
    if ( !this.dragging ) {
      return
    }
  }
  mouseup_handler( e ) {
    this.capture_selection( e )
    this.dragging = false
  }
  keyup_handler( e ) {
    // TODO
    if ( e.shiftKey ) {
      this.capture_selection( e )
    }
  }

  get selection() {
    return this._SELECTION
  }

  makeRangeCopy(obj) {

    function getContainerNodeName(node) {
      // in case the node is #text, go check one level up
      // nodeType => 3 === nodeName => #text
      if (node.nodeType === 3) {
        return node.parentNode.nodeName

      } else {
        return node.nodeName
      }
    }

    function getNodeOffset(obj) {
      // if startContainer => #text
      // and endContainer => p, or something similar
      // eg from text node to a tag node
      // then usually endOffset is 1 but
      // then startOffset can be a bigger value than 1
      // (let's the text selection starts at 4)
      // in this case, it should be easier to construct
      // a regex in the backend by knowing the actual
      // endOffset value in terms of characters, rather
      // than by knowing it has up-traversed 1 node
      // q: doing this in the backend?

      // nodeType => 3 === nodeName => #text

      if (obj.startContainer.nodeType === 3
          && obj.endContainer.nodeName !== undefined) {

        return obj.startContainer.nodeValue.length

      } else {
        return obj.endOffset
      }
    }

    let newObj = {
      start_container: getContainerNodeName(obj.startContainer),
      start_offset: obj.startOffset,
      end_container: getContainerNodeName(obj.endContainer),
      end_offset: getNodeOffset(obj)
    }

    Object.freeze(newObj)

    return newObj;
  }

  getBlockInfo (node) {
    let el = node
    // check if node is of type text
    // if it is, go up one level to select
    // a node of type tag
    if (node.nodeType === 3) {
      el = node.parentNode
    }

    // find nearest node with this class
    // eg find in this case top parent node
    let sectionBlock = el.closest('.block')
    return {
      id: sectionBlock.id,
      type: sectionBlock.dataset.type.split('-').pop()
    }
  }

  set selection({ selection, e }) {
    console.log(selection)
    if ( selection && selection.type === 'Range' ) {

      // range-offset holds the actual good multi-range DOM elements
      // with correct start + end offset
      // we'll send this array to the backend to do more things
      let range = selection.getRangeAt(0)

      if ( range ) {
        this.clear_wrappers()
        let safe_ranges = get_safe_ranges( range )
 
        for ( const safe_range of safe_ranges ) {
          if ( !safe_range.collapsed ) {
            // make copy to send to backend before
            // running range.surroundContents?
            // but then offset is different for any
            // other highlight after *this one*
            let range_copy = this.makeRangeCopy(safe_range)
            this.rangeOffset.push(range_copy)

            this.wrap( safe_range )
          }
        }
      }

      this.toggle_toolbar( range, e )
      const blockInfo = this.getBlockInfo(selection.focusNode)

      this.update_form(blockInfo)
      this.update_window()

    }
    this._SELECTION = selection
  }

  capture_selection( e ) {
    this.selection = {
      selection: window.getSelection(),
      e
    }
  }


  clear_selection() {
    this.selection = null
  }

  create_wrapper() {
    const wrapper = document.createElement( 'div' )
    wrapper.classList.toggle( this.HIGHLIGHT_CLASS )
    return wrapper
  }

  clear_wrappers() {
    if ( this.wrappers.length ) {
      for ( const wrapper of this.wrappers ) {
        wrapper.replaceWith( ...wrapper.childNodes )
        this.wrappers.splice( this.wrappers.indexOf( wrapper ), 0 )
      }
    }
  }

  wrap( range ) {
    const wrapper = this.create_wrapper()
    range.surroundContents( wrapper )
    this.wrappers.push( wrapper )
  }

  toggle_toolbar( range, e ) {
    // if ( Math.abs( range.endOffset - range.startOffset ) > 1 ) {
    if ( this.wrappers.length ) {
      this.toolbar.classList.remove( 'hidden' )
      const wrapper = this.wrappers[ this.wrappers.length - 1 ]
      this.toolbar.style.setProperty( '--top', wrapper.getBoundingClientRect().top + document.documentElement.scrollTop + 'px' )
      this.toolbar.style.setProperty( '--left', wrapper.getBoundingClientRect().left + document.documentElement.scrollLeft + 'px' )
    } else {
      this.toolbar.classList.add( 'hidden' )
      this.toolbar.style.setProperty( '--top', 0 )
      this.toolbar.style.setProperty( '--left', 0 )
    }
  }

  update_form(blockInfo) {
    let form = document.querySelector('.toolbar form')
    form.dataset.blockId = blockInfo.id
    form.dataset.blockSelectionType = blockInfo.type
  }

  update_window() {
    window['selectionObserver'] = this
  }

}


// <https://stackoverflow.com/a/12823606>
// > The solution is to produce an array of smaller Range objects, none of which individually crosses an element barrier, but which collectively cover the Range selected by the user. Each of these safe Ranges can be highlighted as above.

function get_safe_ranges( dangerous ) {

  var a = dangerous.commonAncestorContainer;
  // Starts -- Work inward from the start, selecting the largest safe range
  var s = new Array(0), rs = new Array(0);

  if (dangerous.startContainer != a) {
    for(var i = dangerous.startContainer; i != a; i = i.parentNode) {
      s.push(i)
    }
  };

  if (0 < s.length) for(var i = 0; i < s.length; i++) {
      var xs = document.createRange();
      if (i) {
          xs.setStartAfter(s[i-1]);
          xs.setEndAfter(s[i].lastChild);
      }
      else {
          xs.setStart(s[i], dangerous.startOffset);
          xs.setEndAfter(
              (s[i].nodeType == Node.TEXT_NODE)
              ? s[i] : s[i].lastChild
          );
      }
      rs.push(xs);
  }

  // Ends -- basically the same code reversed
  var e = new Array(0), re = new Array(0);
  if (dangerous.endContainer != a) {
    for(var i = dangerous.endContainer; i != a; i = i.parentNode) {
      e.push(i)
    }
  };

  if (0 < e.length) for(var i = 0; i < e.length; i++) {
      var xe = document.createRange();
      if (i) {
          xe.setStartBefore(e[i].firstChild);
          xe.setEndBefore(e[i-1]);
      }
      else {
          xe.setStartBefore(
              (e[i].nodeType == Node.TEXT_NODE)
              ? e[i] : e[i].firstChild
          );
          xe.setEnd(e[i], dangerous.endOffset);
      }
      re.unshift(xe);
  }

  // Middle -- the uncaptured middle
  if ((0 < s.length) && (0 < e.length)) {
      var xm = document.createRange();
      xm.setStartAfter(s[s.length - 1]);
      xm.setEndBefore(e[e.length - 1]);
  }

  else {
    // if selecting from one node only
    console.log('middle', dangerous)
      return [dangerous];
  }

  // Concat
  rs.push(xm);
  // Send to Console
  return rs.concat(re);
}
