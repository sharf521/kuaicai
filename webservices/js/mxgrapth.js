//添加节点
var drawFlag = true;
function TreeNodeShape() { };
TreeNodeShape.prototype = new mxCylinder();
TreeNodeShape.prototype.constructor = TreeNodeShape;
TreeNodeShape.prototype.segment = 20;
TreeNodeShape.prototype.apply = function (state) {
    mxCylinder.prototype.apply.apply(this, arguments);
    this.state = state;
};
TreeNodeShape.prototype.redrawPath = function (path, x, y, w, h, isForeground) {
    var graph = this.state.view.graph;
    var hasChildren = graph.model.getOutgoingEdges(this.state.cell).length > 0;
    if (isForeground) {
        if (hasChildren) {
            path.moveTo(w / 2, h + this.segment);
            path.lineTo(w / 2, h);
            path.end();
        }
    }
    else {
        path.moveTo(0, 0);
        path.lineTo(w, 0);
        path.lineTo(w, h);
        path.lineTo(0, h);
        path.close();
    }
};
mxCellRenderer.prototype.defaultShapes['treenode'] = TreeNodeShape;
mxGraphView.prototype.updateFloatingTerminalPoint = function (edge, start, end, source) {
    var pt = null;
    if (source) {
        pt = new mxPoint(start.x + start.width / 2,
						start.y + start.height + TreeNodeShape.prototype.segment);
    }
    else {
        pt = new mxPoint(start.x + start.width / 2, start.y);
    }
    edge.setAbsoluteTerminalPoint(pt, source);
};
function main() 
{
    if (!mxClient.isBrowserSupported()) {
        mxUtils.error('浏览器不支持!', 200, false);
    }
    else {
		//$("#drawContent").html("");
        $.ajax({
            type: "post",
            url: "ajax.php",
            data: { func:'getuserjson',type:type,user_id:user_id,user_name:user_name,plevel:plevel,nlevel:nlevel},
            dataType: "json",
            beforeSend: function (XMLHttpRequest) {
                //setPromptPanelVisible();
            },
            success: function (data) {
                mxGraph.prototype.collapsedImage = new mxImage(mxClient.imageBasePath + '/collapsed.gif', 9, 9);
                mxGraph.prototype.expandedImage = new mxImage(mxClient.imageBasePath + '/expanded.gif', 9, 9);
                var container = document.createElement('div');
                container.style.position = '';
                container.style.overflow = '';
                container.style.left = '0px';
                container.style.top = '0px';
                container.style.right = '0px';
                container.style.bottom = '0px';

                if (mxClient.IS_IE) {
                    new mxDivResizer(container);
                }

                $(container).appendTo($("#drawContent"));

                var graph = new mxGraph(container);
                graph.isCellEditable = false;
                var style = graph.getStylesheet().getDefaultVertexStyle();
                style[mxConstants.STYLE_SHAPE] = 'treenode';
                style[mxConstants.STYLE_GRADIENTCOLOR] = 'white';
                style[mxConstants.STYLE_SHADOW] = true;

                style = graph.getStylesheet().getDefaultEdgeStyle();
                style[mxConstants.STYLE_EDGE] = mxEdgeStyle.TopToBottom;
                style[mxConstants.STYLE_ROUNDED] = true;

                graph.setAutoSizeCells(true);
                graph.setPanning(true);
                graph.panningHandler.useLeftButtonForPanning = true;

                var layout = new mxCompactTreeLayout(graph, false);
                layout.useBoundingBox = false;
                layout.edgeRouting = false;
                layout.levelDistance = 30;
                layout.nodeDistance = 10;

                var layoutMgr = new mxLayoutManager(graph);

                layoutMgr.getLayout = function (cell) {
                    if (cell.getChildCount() > 0) {
                        return layout;
                    }
                };
                graph.setCellsSelectable(false);
                graph.isCellFoldable = function (cell) {
                    return this.model.getOutgoingEdges(cell).length > 0;
                };
                graph.cellRenderer.getControlBounds = function (state) {
                    if (state.control != null) {
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
                graph.foldCells = function (collapse, recurse, cells) {
                    this.model.beginUpdate();
                    try {
                        toggleSubtree(this, cells[0], !collapse);
                        this.model.setCollapsed(cells[0], collapse);
                        layout.execute(graph.getDefaultParent());
                    }
                    finally {
                        this.model.endUpdate();
                    }
                };
                var parent = graph.getDefaultParent();
                graph.getModel().beginUpdate();
                try {
                    var w = graph.container.offsetWidth;
                    var node = new Array();
                    for (var i = 0; i < data.length; i++) {
                        
                            if (i == 0) {
                                var vNode = graph.insertVertex(parent, data[i]['user_name'], data[i]['user_name']+'_'+data[i]['user_id']+'\r\n'+data[i]['checktime'], w / 3 - 30, 20, 60, 40);
                                node[data[i]['user_id']] = vNode;
                            } else {
                                vNode = graph.insertVertex(parent, data[i]['user_name'], data[i]['user_name']+'_'+data[i]['user_id']+'\r\n'+data[i]['checktime'], 0, 0, 60, 40);
                                node[data[i]['user_id']] = vNode;
                            }
                       
                    }
                    for (var j = 0; j < data.length; j++) {
                     
                            if (j != 0) {
                                graph.insertEdge(parent, null, '', node[data[j]['tuijianid']], node[data[j]['user_id']]);
                            }
                        
                        graph.updateCellSize(node[data[j]['user_id']]);
                    }
                }
                finally {
                    graph.getModel().endUpdate();
                }
            },
            complete: function (XMLHttpRequest, textStatus) {
                //setPromptPanelHide();
            },
            error: function () {
                //请求出错处理
            }
        });
    }
};
function toggleSubtree(graph, cell, show) {
    show = (show != null) ? show : true;
    var cells = [];

    graph.traverse(cell, true, function (vertex) {
        if (vertex != cell) {
            cells.push(vertex);
        }
        return vertex == cell || !graph.isCellCollapsed(vertex);
    });

    graph.toggleCells(show, cells, true);
};