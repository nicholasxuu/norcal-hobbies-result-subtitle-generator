norcal-hobbies-result-subtitle-generator
========================================

***
Project depreciated, new version can be found at https://github.com/nicholasxuu/RaceTimingSubGenerator
New version has cleaner code (this concept starts from a 3 nights hack), better setup/sync method, and a user interface.
***

a PHP script to generate subtitle for the race info directly from the result sheet.


Video Tutorial:
https://www.youtube.com/watch?v=JyQdFskG1wI


Setup a few variables in main.php, and run the script, you will get a .ass subtitle file generated.
You may then use video editor (i.e. avidemux with subtitle filter) to embed the subtitle into the video. 


Tips:
1. to sync subtitle with video well:

  a. set a general value for the time between the video starts, and the subtitle starts/race start.
  
  b. cut out un-necessary starting part of the video by using A-B point, remove the part not needed.
  
    i.e. If $video_race_start_time is set to 3 (seconds), find the race start tune, 
         and cut out everything 4 seconds before the tune.
         Then go to preview part, move cursor to some other lap, 
         around 1 second before a car cross the start/finish line, 
         find the time difference between the subtitle change and the car crossing the line, 
         (subtitle change 0.3s before the car cross the line)
         and go back to video editing and cut out the time difference,
         (cut 0.3 second in the very beginning of the video).
         Go back to the preview part, you should see the subtitle change right at the time the car cross the line.
         Perfection.
         

2. to organize video file with races:

  a. save subtitle file with race name inside.
  
  b. after loading video into video editor, see what that race is, and choose corresponding subtitle.
  
  c. after finished editing, save movie with same name as subtitle, but with different file extension.
  

3. avidemux settings:

  a. video output: mpeg4 AVC(x264).
  
  b. audio output: mp3(lame). - especially when using video's own audio.

         
