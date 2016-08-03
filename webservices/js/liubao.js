function TreeNodeShape() { };

TreeNodeShape.prototype = new mxCylinder();
TreeNodeShape.prototype.constructor = TreeNodeShape;

// Defines the length of the upper edge segment.
TreeNodeShape.prototype.segment = 20;

// Needs access to the cell state for rendering
TreeNodeShape.prototype.apply = function(state)
{
	mxCylinder.prototype.apply.apply(this, arguments);
	this.state = state;
};

TreeNodeShape.prototype.redrawPath = function(path, x, y, w, h, isForeground)
{
	var graph = this.state.view.graph;
	var hasChildren = graph.model.getOutgoingEdges(this.state.cell).length > 0;
	
	if (isForeground)
	{
		if (hasChildren)
		{
			// Painting outside of vertex bounds is used here
			path.moveTo(w / 2, h + this.segment);
			path.lineTo(w / 2, h);
			path.end();
		}	
	}
	else
	{
		path.moveTo(0, 0);
		path.lineTo(w, 0);
		path.lineTo(w, h);
		path.lineTo(0, h);
		path.close();
	}
};

mxCellRenderer.prototype.defaultShapes['treenode'] = TreeNodeShape;

// Defines a custom perimeter for the nodes in the tree
mxGraphView.prototype.updateFloatingTerminalPoint = function(edge, start, end, source)
{
	var pt = null;
	
	if (source)
	{
		pt = new mxPoint(start.x + start.width / 2,
				start.y + start.height + TreeNodeShape.prototype.segment);
	}
	else
	{
		pt = new mxPoint(start.x + start.width / 2, start.y);
	}

	edge.setAbsoluteTerminalPoint(pt, source);
};
function main()
{
	// Checks if browser is supported
	if (!mxClient.isBrowserSupported())
	{		
		mxUtils.error('浏览器不支持!', 200, false);
	}
	else
	{
		$.ajax({
			type: "post",
			url: "ajax.php",
			data: { func:'getuserjson',type:type,user_id:user_id,user_name:user_name,plevel:plevel,nlevel:nlevel},
			dataType: "json",
			beforeSend: function (XMLHttpRequest) {
				//setPromptPanelVisible();
			},
			success: function (data) 
			{
				
			}
		});
		
		mxGraph.prototype.collapsedImage = new mxImage(mxClient.imageBasePath + '/collapsed.gif', 9, 9);
		mxGraph.prototype.expandedImage = new mxImage(mxClient.imageBasePath + '/expanded.gif', 9, 9);
		
		// Workaround for Internet Explorer ignoring certain styles
		var container = document.createElement('div');
		container.style.position = 'absolute';
		container.style.overflow = 'hidden';
		container.style.left = '0px';
		container.style.top = '0px';
		container.style.right = '0px';
		container.style.bottom = '0px';
		
		if (mxClient.IS_IE)
		{
			new mxDivResizer(container);
		}
		
		document.body.appendChild(container);
	
		// Creates the graph inside the given container
		var graph = new mxGraph(container);

		// Set some stylesheet options for the visual appearance
		var style = graph.getStylesheet().getDefaultVertexStyle();
		style[mxConstants.STYLE_SHAPE] = 'treenode';
		style[mxConstants.STYLE_GRADIENTCOLOR] = 'white';
		style[mxConstants.STYLE_SHADOW] = true;
		
		style = graph.getStylesheet().getDefaultEdgeStyle();
		style[mxConstants.STYLE_EDGE] = mxEdgeStyle.TopToBottom;
		style[mxConstants.STYLE_ROUNDED] = true;
		
		// Enables automatic sizing for vertices after editing and
		// panning by using the left mouse button.
		graph.setAutoSizeCells(true);
		graph.setPanning(true);
		graph.panningHandler.useLeftButtonForPanning = true;

		// Stops editing on enter or escape keypress
		var keyHandler = new mxKeyHandler(graph);
		
		// Enables automatic layout on the graph and installs
		// a tree layout for all groups who's children are
		// being changed, added or removed.
		var layout = new mxCompactTreeLayout(graph, false);
		layout.useBoundingBox = false;
		layout.edgeRouting = false;
		layout.levelDistance = 30;
		layout.nodeDistance = 10;

		var layoutMgr = new mxLayoutManager(graph);
		
		layoutMgr.getLayout = function(cell)
		{
			if (cell.getChildCount() > 0)
			{
				return layout;
			}
		};

		// Disallow any selections
		graph.setCellsSelectable(false);

		// Defines the condition for showing the folding icon
		graph.isCellFoldable = function(cell)
		{
			return this.model.getOutgoingEdges(cell).length > 0;
		};

		// Defines the position of the folding icon
		graph.cellRenderer.getControlBounds = function(state)
		{
			if (state.control != null)
			{
				var oldScale = state.control.scale;
				var w = state.control.bounds.width / oldScale;
				var h = state.control.bounds.height / oldScale;
				var s = state.view.scale;			

				return new mxRectangle(state.x + state.width / 2 - w / 2 * s,
					state.y + state.height + TreeNodeShape.prototype.segment * s - h / 2 * s,
					w * s, h * s);
			}
			
			return null;
		};

		// Implements the click on a folding icon
		graph.foldCells = function(collapse, recurse, cells)
		{
			this.model.beginUpdate();
			try
			{
				toggleSubtree(this, cells[0], !collapse);
				this.model.setCollapsed(cells[0], collapse);

				// Executes the layout for the new graph since
				// changes to visiblity and collapsed state do
				// not trigger a layout in the current manager.
				layout.execute(graph.getDefaultParent());
			}
			finally
			{
				this.model.endUpdate();
			}
		};
		
		// Gets the default parent for inserting new cells. This
		// is normally the first child of the root (ie. layer 0).
		var parent = graph.getDefaultParent();
						
		// Adds the root vertex of the tree
		graph.getModel().beginUpdate();
		try
		{
			var node = new Array();
			for (var i = 0; i < data.length; i++) 
			{
					//data[i]['user_name']
			   
			}
			for (var j = 0; j < data.length; j++) 
			{			 
					
			}
			
			var w = graph.container.offsetWidth;
			var root = graph.insertVertex(parent, 'treeRoot', 'Root', w/2 - 30, 20, 60, 40);

			var v1 = graph.insertVertex(parent, 'v1', 'Child 1', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', root, v1);
			
			var v2 = graph.insertVertex(parent, 'v2', 'Child 2', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', root, v2);

			var v3 = graph.insertVertex(parent, 'v3', 'Child 3', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', root, v3);
			
			var v11 = graph.insertVertex(parent, 'v11', 'Child 1.1', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v1, v11);
			
			var v12 = graph.insertVertex(parent, 'v12', 'Child 1.2', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v1, v12);
			
			var v21 = graph.insertVertex(parent, 'v21', 'Child 2.1', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v2, v21);
			
			var v22 = graph.insertVertex(parent, 'v22', 'Child 2.2', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v2, v22);
			
			var v221 = graph.insertVertex(parent, 'v221', 'Child 2.2.1', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v22, v221);
			
			var v222 = graph.insertVertex(parent, 'v222', 'Child 2.2.2', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v22, v222);

			var v31 = graph.insertVertex(parent, 'v31', 'Child 3.1', 0, 0, 60, 40);
			graph.insertEdge(parent, null, '', v3, v31);
		}
		finally
		{
			// Updates the display
			graph.getModel().endUpdate();
		}
	}
};

// Updates the visible state of a given subtree taking into
// account the collapsed state of the traversed branches
function toggleSubtree(graph, cell, show)
{
	show = (show != null) ? show : true;
	var cells = [];
	
	graph.traverse(cell, true, function(vertex)
	{
		if (vertex != cell)
		{
			cells.push(vertex);
		}

		// Stops recursion if a collapsed cell is seen
		return vertex == cell || !graph.isCellCollapsed(vertex);
	});

	graph.toggleCells(show, cells, true);
};
//==============================================