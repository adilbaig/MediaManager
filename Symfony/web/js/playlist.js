/**
 * The Playlist instance represents an auto playing list
 * A playlist is a UL, with A tags. This component binds links in the list, and 
 * continues playing from the current position.
 * 
 * Note : The Playlist can be abstracted even further to remove the UI specific
 * portions completely. Tracks can be passed as an array of strings, and the UI
 * of the playlist can keep up by listening to events generated from the playlist.
 * 
 * @param DOMElement audio
 * @param JQueryElement playlist
 * 
 * @returns {Playlist}
 */
function Playlist(audio, playlist) {
    var current = 0;
    var len = playlist.find('li a').length;
    
    function playTrack(index) {
        if(index > len - 1) {
            index = 0;
        }

        var li = $(playlist.find('li')[index]);
        li.addClass('active').siblings().removeClass('active');
        play(audio, li.find('a').attr('href'));
    }
    
    function nextTrack() {
        current++;
        if(current > len - 1) {
            current = 0;
        }
        
        playTrack(current);
    }
    
    playlist.find('a').click(function(e){
        e.preventDefault();
        current = $(this).parent().index();
        playTrack(current);
    });
    
    audio.addEventListener('ended',function(e){
        nextTrack();
    });
    
    this.start = function() {
        playTrack(current);
    }
}

function play(audio, url) {
    audio.src = url;
    audio.volume = .40;
    audio.load();
    audio.play();
}

playlist = new Playlist($('audio')[0], $('#playlist'));
playlist.start();
