// Register the related commands.
//FCKDialogCommand(命令名称,对话框标题,URl,宽度，高度) 
var dialogPath = FCKConfig.PluginsPath + 'uploader/upload.php';
var uploaerDialogCmd = new FCKDialogCommand('Uploader', FCKLang["DlgUploderTitle"], dialogPath, 400, 280 );
FCKCommands.RegisterCommand( 'Uploader', uploaerDialogCmd ) ;
// Create toolbar button.
var oUploader		= new FCKToolbarButton( 'Uploader', FCKLang["DlgUploderTitle"]) ;
oUploader.IconPath	= FCKPlugins.Items['Uploader'].Path + 'uploader.gif' ;
FCKToolbarItems.RegisterItem( 'Uploader', oUploader ) ;	