# namespace object.. TODO: pick a better name
ns = {}
# to load from json file
stations_data = {}

trainlines = {}

config =
  spacing:
    x: 30
    y: 25
  textspacing:
    x: 20
    y: 16
  ver_text_pos: 'left'
  label_spacing: 20
  path_bend_radius: 13
  start:
    x: 165
    y: 18

circ_config =
  'stroke-width': 4
  # stroke: '#7fb400'
  r: 8 #radius
  fill: '#fff'
  
colors =
  sukhumvit:
    stroke: '#7fb400'
  silom:
    stroke: '#007878'
  mrt:
    stroke: '#1f50c4'
  mrtp:
    stroke: '#8d4196'
  ap_link:
    stroke: '#cc5050'
    
circ_config_2 =
  'stroke-width': 4
  #stroke: '#f00'
  fill: '#fff'
  r: 10 #radius
  
# used for normal state (unhovered)
text_config = 
  'font-size': 12
  'fill': '#000'

# used for hover state
text_config_2 =
  'fill': '#000'
  'font-size': 14
  
path_config =
  #'stroke': circ_config.stroke
  'stroke-width': 10

# vertical style overrides
ver_text_config =
  'text-anchor': 'end'

# horizontal style overrides
hor_text_config =
  'text-anchor': 'start'
  
  
### END CONFIGS ###
  
  

# creates a circle on screen
addcirc = (paper, x, y, radius) ->
  circle = paper.circle(x, y, radius)
  circle.attr(circ_config)
  
  # add to set
  ns.drawings.push(circle)
  ns.drawings_and_text.push(circle)
  ns.circles.push(circle)
  
  circle

  
# creates a text object
addtext = (paper, x, y, text, attrs) ->
  text = paper.text(x, y, text)
  text.attr(attrs)
  ns.texts.push(text)
  ns.drawings_and_text.push(text)
  text


getx = (current, dir='hor') ->
  if dir is "hor" or dir is "dgn"
    x = current.x + config.spacing.x
  else if dir is "ver"
    x = current.x
  x

  
gety = (current, dir='hor') ->
  if dir is "hor"
    y = current.y
  else if dir is "ver" or dir is "dgn"
    y = current.y + config.spacing.y
  y

  
getnextpos = (start, dir) ->
  x: getx(start, dir)
  y: gety(start, dir)
  
# adds a bts station on screen
addbts = (paper, bts, current, dir="hor") ->
  circ_x = current.x
  circ_y = current.y
  
  text_x = circ_x
  text_y = circ_y
  
  text_attrs = text_config
  
  if dir is 'hor'
    text_y -= config.textspacing.x
    
  if dir is 'ver'
    if config.ver_text_pos is 'left'
      text_x -= config.textspacing.y
    else
      text_x += config.textspacing.y
    
  circ = addcirc(paper, circ_x, circ_y, circ_config.r)
  text = addtext(paper, text_x, text_y, bts.name, text_attrs)
  
  # keep a reference to the text counterpart and bts object
  circ.mytext = text
  circ.bts = bts
  
  for key, value of text_config
    text.attr(key, value)
  
  if dir is 'ver'
    for key, value of ver_text_config
      text.attr(key, value)
  
  if dir is 'hor'
    # rotate text for horizontal layout
    text.attr('transform', 'r-45 ' + text_x + ' ' + text_y)
    for key, value of hor_text_config
      text.attr(key, value)
  
  text.attr(text_attrs)
  
# draw a line from (x1, y1) to (x2, y2) on paper
drawline = (paper, x1, y1, x2, y2) ->
  # (arc) A rx ry x-axis-rotation large-arc-flag sweep-flag x y
  # line = paper.path("M300, 300 A30,30 0 0 1 330,330 ")
  line = paper.path("M#{x1} #{y1}L#{x2} #{y2}")
  line.attr path_config
  ns.drawings.push(line)
  ns.drawings_and_text.push(line)
  line


drawcurve = (paper, x1, y1, x2, y2, radius) ->
  line = paper.path("M#{x1}, #{y1} A#{radius},#{radius} 0 0 1 #{x2},#{y2} ")
  line.attr path_config
  
  ns.drawings.push(line)
  ns.drawings_and_text.push(line)
  
  line

min = (x, y) ->
  if x < y then x else y
  
drawcurve2 = (paper, x1, y1, x2, y2, radius, corner = 'nw') ->
  
  if corner is 'ne'
    line1 = paper.path("M#{x1}, #{y1} L#{x2 - radius}, #{y1}")
    line2 = paper.path("M#{x2}, #{y1 + radius} L#{x2}, #{y2}")
  
    drawcurve(paper, x2 - radius, y1, x2, y1 + radius, radius)
    
  else if corner is 'sw'
    line1 = paper.path("M#{x1}, #{y1} L#{x1}, #{y1 + config.spacing.y - radius}")
    line2 = paper.path("M#{x2 - config.spacing.x + radius}, #{y2} L#{x2}, #{y2}")
  
    drawcurve(paper, x2 - config.spacing.x + radius, y1 + config.spacing.y, x2 - config.spacing.x, y1 + config.spacing.y - radius, radius)
    
  line1.attr(path_config)
  line2.attr(path_config)
  
  ns.drawings.push(line1)
  ns.drawings.push(line2)
  
  ns.drawings_and_text.push(line1)
  ns.drawings_and_text.push(line2)
  
drawlineandstations = (paper, train_line, stations_data, dir, config) ->
  stations = stations_data[train_line].stations

  if not stations?
    console.warn "stations_data does not have trainline '#{train_line}'"
    return

  for station, i in stations
    break_line = i in config.breaks

    # northeast
    corner = 'ne'
    
    if dir is 'ver' and break_line
      corner = 'sw' #southwest
    
    if i < stations.length - 1
      if (break_line)
        to = getnextpos(config.start, 'dgn') # dgn means diagonal
      else
        to = getnextpos(config.start, dir)
      
      # draw curve or line
      if not break_line
        drawline(
          paper,
          config.start.x, config.start.y
          to.x, to.y
        )
      else
        drawcurve2(
          paper,
          config.start.x, config.start.y
          to.x, to.y,
          config.path_bend_radius,
          corner
        )
      
    addbts(paper, station, config.start, dir)
    config.start = to
    
    if break_line
      # flip direction
      dir = if dir is 'hor' then 'ver' else 'hor'
     
  stations


initialize_paper = (element) ->
  # this is what we will draw on
  paper = Raphael(element)

  # container for all drawings
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  ns.texts = paper.set()
  ns.circles = paper.set()

  paper


draw_sukhumvit = (paper) ->
  drawlineandstations(paper, 'sukhumvit', stations_data, 'ver', config)
  trainlines.sukhumvit = ns.drawings_and_text
  ns.drawings.attr(colors.sukhumvit)
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  ns.drawings.attr(colors.sukhumvit)
  paper

draw_silom = (paper) ->
  drawlineandstations(paper, 'silom', stations_data, 'hor', config)
  trainlines.silom = ns.drawings_and_text
  ns.drawings.attr(colors.silom)
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  paper

draw_mrt = (paper) ->
  drawlineandstations(paper, 'mrt', stations_data, 'hor', config)
  trainlines.mrt = ns.drawings_and_text
  ns.drawings.attr(colors.mrt)
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  paper

draw_mrtp = (paper) ->
  drawlineandstations(paper, 'mrtp', stations_data, 'hor', config)
  trainlines.mrtp = ns.drawings_and_text
  ns.drawings.attr(colors.mrtp)
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  paper
  
draw_aplink = (paper) ->
  drawlineandstations(paper, 'ap_link', stations_data, 'hor', config)
  trainlines.ap_link = ns.drawings_and_text
  ns.drawings.attr(colors.ap_link)
  ns.drawings = paper.set()
  ns.drawings_and_text = paper.set()
  paper

hook_events = () ->
  # hover event
  ns.circles.hover( () ->
      this.animate circ_config_2, 50
      @mytext.animate text_config_2, 50
    ,
    () ->
      @animate circ_config, 100
      @mytext.animate text_config, 100
  )

  # click event
  ns.circles.click( () ->
    controllerElement = document.querySelector('#front-page-search-form')
    controllerScope = angular.element(controllerElement).scope()

    controllerScope.searchfilter.latitude = @bts.lat
    controllerScope.searchfilter.longitude = @bts.lng
    controllerScope.searchfilter.transport_name = @bts.name
    controllerScope.searchfilter.transport_station = @bts.id
    controllerScope.searchfilter.order='ORDER_BY_NEAREST_FIRST'
    controllerScope.savefilter();
    controllerScope.$apply()

    controllerElement.submit();
  )

  # hide initially
  for k, v of trainlines
    v.attr
      opacity: 0

  # buttons to show/hide train lines
  $('.show_stations').click( (e) ->
    e.preventDefault()
    train_line = $(this).data('train-line')

    controllerElement = document.querySelector('#front-page-search-form')
    controllerScope = angular.element(controllerElement).scope()
    controllerScope.searchfilter.transport_line = train_line
    controllerScope.$apply()

    for k, v of trainlines when k isnt train_line
      v.animate
        opacity: 0
        200
        'linear'
        do (k, v) ->
          () ->
            v.hide()
    
    trainlines[train_line]
      .show()
      .animate
        opacity: 1
        200
  )

  # default: sukhumvit
  $('.show_stations[data-train-line="sukhumvit"]').trigger('click')

  return # return nothing for hook_events

""" Given a JSON object, `name` is set to `name_{lang}`"""
translate = (obj, lang = 'en') ->
  lang_key = 'name_' + lang
  if obj[lang_key]?
    obj['name'] = obj[lang_key]

  return

drawstations = (paper, stations) ->
  if stations?
    # translate each line (pass by reference)
    lang = if window.langHalf? then window.langHalf else 'en'
    for key, line of stations
      translate(line, lang)
      for station in line.stations
        translate(station, lang)

  # SUKHUMVIT
  config.breaks = [6]
  config.start = {x: 140, y: 35}
  config.spacing = {x: 35, y: 23}
  draw_sukhumvit(paper)

  # SILOM
  config.start = {x: 155, y: 193}
  config.spacing = {x: 40, y: 25}
  config.breaks = []

  draw_silom(paper)

  # MRT
  config.start = {x: 40, y: 193}
  config.spacing = {x: 35, y: 50}

  draw_mrt(paper)

  # MRT Purple Line
  config.start = {x: 40, y: 193}
  config.spacing = {x: 35, y: 50}

  draw_mrtp(paper)

  # AIRPORT LINK
  config.breaks = [6]
  config.start = {x: 170, y: 140}
  config.spacing = {x: 50, y: 50}

  draw_aplink(paper)

  hook_events()

  return


# @ means "this" and this is "window".. we're making a global function
@loadBtsMap = (element, url, callback=null, error=null) ->
  if not url? then return

  $.ajax(url, {dataType: 'json'})
    .done( (data) ->
      stations_data = data

      if not stations_data?
        console.error("Invalid stations data")
        return

      paper = initialize_paper(element, 720, 220)

      drawstations(paper, stations_data)

      if callback?
        callback.apply()

      return
    )
    .fail( () ->
      console.error("Error loading stations json file")
      if error?
        error.apply()
    )

  return

