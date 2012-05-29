var fluid_1_4=fluid_1_4||{};(function($,fluid){fluid.uploader=fluid.uploader||{};var startUploading;var updateProgress=function(file,events,demoState,isUploading){if(!isUploading){return }var chunk=Math.min(demoState.chunkSize,file.size);demoState.bytesUploaded=Math.min(demoState.bytesUploaded+chunk,file.size);events.onFileProgress.fire(file,demoState.bytesUploaded,file.size)};var finishAndContinueOrCleanup=function(that,file){that.queue.finishFile(file);that.events.afterFileComplete.fire(file);if(that.queue.shouldUploadNextFile()){startUploading(that)}else{that.events.afterUploadComplete.fire(that.queue.currentBatch.files);if(file.status!==fluid.uploader.fileStatusConstants.CANCELLED){that.queue.clearCurrentBatch()}}};var finishUploading=function(that){if(!that.queue.isUploading){return }var file=that.demoState.currentFile;that.events.onFileSuccess.fire(file);that.demoState.fileIdx++;finishAndContinueOrCleanup(that,file)};var simulateUpload=function(that){if(!that.queue.isUploading){return }var file=that.demoState.currentFile;if(that.demoState.bytesUploaded<file.size){fluid.invokeAfterRandomDelay(function(){updateProgress(file,that.events,that.demoState,that.queue.isUploading);simulateUpload(that)})}else{finishUploading(that)}};startUploading=function(that){that.demoState.currentFile=that.queue.files[that.demoState.fileIdx];that.demoState.chunksForCurrentFile=Math.ceil(that.demoState.currentFile/that.demoState.chunkSize);that.demoState.bytesUploaded=0;that.queue.isUploading=true;that.events.onFileStart.fire(that.demoState.currentFile);simulateUpload(that)};var stopDemo=function(that){var file=that.demoState.currentFile;file.filestatus=fluid.uploader.fileStatusConstants.CANCELLED;that.queue.shouldStop=true;that.events.onFileError.fire(file,fluid.uploader.errorConstants.UPLOAD_STOPPED,"The demo upload was paused by the user.");finishAndContinueOrCleanup(that,file);that.events.onUploadStop.fire()};var setupDemo=function(that){if(that.simulateDelay===undefined||that.simulateDelay===null){that.simulateDelay=true}that.demoState={fileIdx:0,chunkSize:200000};return that};fluid.uploader.demoRemote=function(queue,options){var that=fluid.initLittleComponent("fluid.uploader.demoRemote",options);that.queue=queue;that.uploadNextFile=function(){startUploading(that)};that.stop=function(){stopDemo(that)};setupDemo(that);return that};fluid.invokeAfterRandomDelay=function(fn){var delay=Math.floor(Math.random()*1000+100);setTimeout(fn,delay)};fluid.defaults("fluid.uploader.demoRemote",{gradeNames:["fluid.eventedComponent"],argumentMap:{options:1},events:{onFileProgress:"{multiFileUploader}.events.onFileProgress",afterFileComplete:"{multiFileUploader}.events.afterFileComplete",afterUploadComplete:"{multiFileUploader}.events.afterUploadComplete",onFileSuccess:"{multiFileUploader}.events.onFileSuccess",onFileStart:"{multiFileUploader}.events.onFileStart",onFileError:"{multiFileUploader}.events.onFileError",onUploadStop:"{multiFileUploader}.events.onUploadStop"}});fluid.demands("fluid.uploader.remote",["fluid.uploader.multiFileUploader","fluid.uploader.demo"],{funcName:"fluid.uploader.demoRemote",args:["{multiFileUploader}.queue","{multiFileUploader}.events",fluid.COMPONENT_OPTIONS]})})(jQuery,fluid_1_4);