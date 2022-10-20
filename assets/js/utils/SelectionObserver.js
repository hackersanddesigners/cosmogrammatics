export default class SelectionObserver {

  HIGHLIGHT_CLASS = "highlight"
  _SELECTION = null
  wrappers = []
  dragging = false

  constructor( target ) {
    for ( const event of [
      'mousedown',
      'mousemove',
      'mouseup',
      'keyup'
    ]) {
      target.addEventListener( event, e => { this[ `${ event }_handler` ]( e ) } )
    }
    this.target = target
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
  set selection( selection ) {
    if ( selection ) {

      this.clear_wrappers()
      let safe_ranges = get_safe_ranges( selection )
      for ( const safe_range of safe_ranges ) {
        this.wrap( safe_range )
      }



    }
    this._selection = selection
  }

  capture_selection( e ) {
    this.selection = window.getSelection().getRangeAt(0)
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


}


// https://stackoverflow.com/a/12823606

function get_safe_ranges( dangerous ) {
  var a = dangerous.commonAncestorContainer;
  // Starts -- Work inward from the start, selecting the largest safe range
  var s = new Array(0), rs = new Array(0);
  if (dangerous.startContainer != a)
      for(var i = dangerous.startContainer; i != a; i = i.parentNode)
          s.push(i)
  ;
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
  if (dangerous.endContainer != a)
      for(var i = dangerous.endContainer; i != a; i = i.parentNode)
          e.push(i)
  ;
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
      return [dangerous];
  }

  // Concat
  rs.push(xm);
  // Send to Console
  return rs.concat(re);
}
