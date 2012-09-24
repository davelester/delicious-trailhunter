UC Berkeley Scavenger Hunt
=====================

A scavenger hunt for UC Berkeley's campus, powered by the Delicious API and completed as an assignment for [INFO 290, Information Organization Lab](http://courses.ischool.berkeley.edu/i290-iol/f12/). A live version of this is running at [http://krushton.com](http://krushton.com/).

## Project Team and Roles
* [Michael Hintze](http://michaelhintze.com) -- CSS and front-end rockstar
* [Dave Lester](http://davelester.org) -- Data integration and JS wizard
* [Kate Rushton](http://krushton.com) -- Delicious API, Backend, and PHP ninja

## What's Under the Hood

### Technologies Used
Javascript, PHP, HTML, CSS

### Delicious Username
 * [iolabtrailhunter](http://www.delicious.com/iolabtrailhunter)

### Browser Support
Safari 6.0.1, Chrome 21.0.1180.89

### Bugs, Quirks, Easter Eggs
1. Clearing local storage does not always work, as a result users who complete the game or want to start a new one will be caught between the leaderboard the current trail list. 

2. Our checkin function is too stringent about location. You have to be very close to the target to get a succesful checkin. 

3. Audio file on the activity stream does not automatically stop playing when the user leaves the page.
