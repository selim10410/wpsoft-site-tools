(function(){
'use strict';

function all(root, selector){
    return Array.prototype.slice.call((root || document).querySelectorAll(selector));
}

function addSwipe(el,onPrev,onNext){
    if(!el || el.dataset.wpstSwipeReady==='1') return;
    el.dataset.wpstSwipeReady='1';
    var sx=0,sy=0,dx=0,drag=false,pointerId=null;
    var threshold=42;

    function start(x,y,id){
        sx=x;sy=y;dx=0;drag=true;pointerId=id===undefined?null:id;
        el.classList.add('is-touching');
    }
    function move(x,y){
        if(!drag)return;
        dx=x-sx;
        var dy=y-sy;
        if(Math.abs(dx)>10 && Math.abs(dx)>Math.abs(dy)) el.classList.add('is-horizontal-gesture');
    }
    function end(x,y){
        if(!drag)return;
        dx=x-sx;
        var dy=y-sy;
        drag=false;pointerId=null;
        el.classList.remove('is-touching','is-horizontal-gesture');
        if(Math.abs(dx)>=threshold && Math.abs(dx)>Math.abs(dy)*1.15){
            el.dataset.wpstSwipeJustMoved='1';
            window.setTimeout(function(){delete el.dataset.wpstSwipeJustMoved;},360);
            if(dx>0 && typeof onPrev==='function')onPrev();
            if(dx<0 && typeof onNext==='function')onNext();
        }
    }

    if('PointerEvent' in window){
        el.addEventListener('pointerdown',function(e){
            /* Mouse dragging is handled separately by widgets.
               Generic swipe is for touch/pen to avoid duplicate desktop transitions. */
            if(e.pointerType==='mouse')return;
            if(el.dataset.touchSwipe==='no')return;
            start(e.clientX,e.clientY,e.pointerId);
        },{passive:true});
        el.addEventListener('pointermove',function(e){
            if(pointerId!==null && e.pointerId!==pointerId)return;
            move(e.clientX,e.clientY);
        },{passive:true});
        el.addEventListener('pointerup',function(e){
            if(pointerId!==null && e.pointerId!==pointerId)return;
            end(e.clientX,e.clientY);
        },{passive:true});
        el.addEventListener('pointercancel',function(){drag=false;el.classList.remove('is-touching','is-horizontal-gesture')},{passive:true});
    }else{
        el.addEventListener('touchstart',function(e){if(el.dataset.touchSwipe==='no')return;var t=e.changedTouches[0];if(t)start(t.clientX,t.clientY,null)},{passive:true});
        el.addEventListener('touchmove',function(e){var t=e.changedTouches[0];if(t)move(t.clientX,t.clientY)},{passive:true});
        el.addEventListener('touchend',function(e){var t=e.changedTouches[0];if(t)end(t.clientX,t.clientY)},{passive:true});
    }
}

function initImageSlider(root){
    all(root,'.wpst-ew-image-slider').forEach(function(slider){
        var slides=all(slider,'.wpst-image-slider-slide');
        if(!slides.length)return;
        var signature=String(slides.length);
        if(slider.dataset.wpstImageSliderReady===signature)return;
        slider.dataset.wpstImageSliderReady=signature;

        var index=0,timer=null,dragging=false,dragStartX=0,dragLastX=0;
        var prev=slider.querySelector('.wpst-image-slider-prev');
        var next=slider.querySelector('.wpst-image-slider-next');
        var dots=slider.querySelector('.wpst-image-slider-dots');
        var progress=slider.querySelector('.wpst-image-slider-progress>span');
        var delay=Math.max(1500,parseInt(slider.dataset.delay||4500,10));

        function stop(){if(timer){clearInterval(timer);timer=null;}}
        function start(){
            stop();
            if(slider.dataset.autoplay==='yes'&&slides.length>1&&!document.hidden){
                timer=setInterval(function(){show(index+1,false);},delay);
            }
        }
        function show(n,restart){
            index=(n+slides.length)%slides.length;
            slides.forEach(function(slide,i){
                var on=i===index;
                slide.classList.toggle('is-active',on);
                slide.setAttribute('aria-hidden',on?'false':'true');
            });
            if(dots){
                all(dots,'button').forEach(function(b,i){
                    var on=i===index;
                    b.classList.toggle('is-active',on);
                    b.setAttribute('aria-current',on?'true':'false');
                });
            }
            if(progress)progress.style.width=Math.min(100,((index+1)/slides.length)*100)+'%';
            if(restart!==false)start();
        }

        if(dots){
            dots.innerHTML='';
            slides.forEach(function(_,i){
                var b=document.createElement('button');
                b.type='button';
                b.setAttribute('aria-label','Görsel '+(i+1));
                b.addEventListener('click',function(){show(i,true);});
                dots.appendChild(b);
            });
        }

        if(prev)prev.addEventListener('click',function(){show(index-1,true);});
        if(next)next.addEventListener('click',function(){show(index+1,true);});

        addSwipe(slider,function(){show(index-1,true);},function(){show(index+1,true);});

        slider.addEventListener('keydown',function(e){
            if(e.key==='ArrowLeft'){e.preventDefault();show(index-1,true);}
            if(e.key==='ArrowRight'){e.preventDefault();show(index+1,true);}
            if(e.key==='Home'){e.preventDefault();show(0,true);}
            if(e.key==='End'){e.preventDefault();show(slides.length-1,true);}
        });

        if(slider.dataset.mouseDrag==='yes'){
            var dragMoved=false;
            slider.addEventListener('mousedown',function(e){
                if(e.button!==0||e.target.closest('button,input,textarea,select'))return;
                dragging=true;dragMoved=false;dragStartX=e.clientX;dragLastX=e.clientX;
                slider.classList.add('is-mouse-dragging');stop();
                if(e.preventDefault)e.preventDefault();
            });
            window.addEventListener('mousemove',function(e){
                if(!dragging)return;
                dragLastX=e.clientX;
                if(Math.abs(dragLastX-dragStartX)>8)dragMoved=true;
            });
            window.addEventListener('mouseup',function(){
                if(!dragging)return;
                var dx=dragLastX-dragStartX;
                dragging=false;slider.classList.remove('is-mouse-dragging');
                if(Math.abs(dx)>=46){
                    slider.dataset.wpstSwipeJustMoved='1';
                    window.setTimeout(function(){delete slider.dataset.wpstSwipeJustMoved;},360);
                    show(index+(dx<0?1:-1),true);
                }else start();
            });
            slider.addEventListener('click',function(e){
                if(slider.dataset.wpstSwipeJustMoved==='1' && e.target.closest('a')){
                    e.preventDefault();
                    e.stopPropagation();
                }
            },true);
            slider.addEventListener('dragstart',function(e){
                if(e.target.closest('img,a'))e.preventDefault();
            });
        }

        /* Touch swipe over linked images must change slide, not accidentally open the link. */
        slider.addEventListener('click',function(e){
            if(slider.dataset.wpstSwipeJustMoved==='1' && e.target.closest('a')){
                e.preventDefault();
                e.stopPropagation();
            }
        },true);

        if(slider.dataset.pauseHover==='yes'){
            slider.addEventListener('mouseenter',stop);
            slider.addEventListener('mouseleave',start);
        }
        slider.addEventListener('focusin',stop);
        slider.addEventListener('focusout',function(){setTimeout(function(){if(!slider.contains(document.activeElement))start();},0);});
        document.addEventListener('visibilitychange',function(){if(document.hidden)stop();else start();});

        show(0,false);
        start();
    });
}

function initHeroSlider(root){
    all(root,'.wpst-ew-hero-slider').forEach(function(slider){
        var slides = all(slider,'.wpst-ew-hero-slide');
        var signature = String(slides.length);
        if(slider.dataset.wpstSliderReady === signature) return;
        slider.dataset.wpstSliderReady = signature;
        if(!slides.length) return;

        var index = 0;
        var dotsWrap = slider.querySelector('.wpst-ew-slider-dots');
        var prev = slider.querySelector('.wpst-ew-slider-prev');
        var next = slider.querySelector('.wpst-ew-slider-next');
        var progress = slider.querySelector('.wpst-ew-slider-progress>span');
        var counterCurrent = slider.querySelector('.wpst-ew-slider-counter>span');
        var timer = null;
        var delay = parseInt(slider.dataset.delay || '5000',10);
        if(!delay || delay < 1000) delay = 5000;
        var dragging=false,dragStartX=0,dragLastX=0;

        function pad(n){return String(n).padStart(2,'0');}

        function paint(n,restartTimer){
            var previous=index;
            index = (n + slides.length) % slides.length;

            slider.classList.toggle('is-direction-next',index>=previous);
            slider.classList.toggle('is-direction-prev',index<previous);

            slides.forEach(function(slide,i){
                var on=i===index;
                slide.classList.toggle('is-active',on);
                slide.setAttribute('aria-hidden',on?'false':'true');
            });

            if(dotsWrap){
                all(dotsWrap,'button').forEach(function(dot,i){
                    var on=i===index;
                    dot.classList.toggle('is-active',on);
                    dot.setAttribute('aria-current',on?'true':'false');
                });
            }

            if(counterCurrent)counterCurrent.textContent=pad(index+1);
            if(progress){
                progress.style.width=Math.min(100,((index+1)/Math.max(1,slides.length))*100)+'%';
            }

            if(restartTimer!==false)start();
        }

        function stop(){
            if(timer){
                clearInterval(timer);
                timer = null;
            }
        }

        function start(){
            stop();
            if(slider.dataset.autoplay === 'yes' && slides.length > 1 && !document.hidden){
                timer = setInterval(function(){ paint(index + 1,false); }, delay);
            }
        }

        if(dotsWrap){
            dotsWrap.innerHTML = '';
            slides.forEach(function(_,i){
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.setAttribute('aria-label','Slayt '+(i+1));
                dot.addEventListener('click',function(){paint(i,true);});
                dotsWrap.appendChild(dot);
            });
        }

        if(prev)prev.addEventListener('click',function(){paint(index - 1,true);});
        if(next)next.addEventListener('click',function(){paint(index + 1,true);});

        if(slider.dataset.pauseHover === 'yes'){
            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);
        }

        slider.addEventListener('focusin',stop);
        slider.addEventListener('focusout',function(){
            setTimeout(function(){if(!slider.contains(document.activeElement))start();},0);
        });

        document.addEventListener('visibilitychange',function(){
            if(document.hidden)stop();else start();
        });

        slider.addEventListener('keydown',function(e){
            if(e.key === 'ArrowLeft'){e.preventDefault();paint(index - 1,true);}
            if(e.key === 'ArrowRight'){e.preventDefault();paint(index + 1,true);}
            if(e.key === 'Home'){e.preventDefault();paint(0,true);}
            if(e.key === 'End'){e.preventDefault();paint(slides.length-1,true);}
        });

        addSwipe(slider,function(){paint(index-1,true);},function(){paint(index+1,true);});

        if(slider.dataset.mouseDrag==='yes'){
            slider.addEventListener('mousedown',function(e){
                if(e.button!==0 || e.target.closest('a,button,input,textarea,select'))return;
                dragging=true;
                dragStartX=e.clientX;
                dragLastX=e.clientX;
                slider.classList.add('is-mouse-dragging');
                stop();
            });
            window.addEventListener('mousemove',function(e){
                if(dragging)dragLastX=e.clientX;
            });
            window.addEventListener('mouseup',function(){
                if(!dragging)return;
                var dx=dragLastX-dragStartX;
                dragging=false;
                slider.classList.remove('is-mouse-dragging');
                if(Math.abs(dx)>=48)paint(index+(dx<0?1:-1),true);
                else start();
            });
        }

        slider.setAttribute('role','region');
        slider.setAttribute('aria-roledescription','carousel');
        if(!slider.hasAttribute('tabindex'))slider.tabIndex=0;
        paint(0,false);
        start();
    });
}

function initTestimonials(root){
    all(root,'.wpst-ew-testimonial-slider').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var items = all(w,'.wpst-ew-testimonial-track article');
        if(!items.length) return;
        var i = 0, timer = null;
        var dots = w.querySelector('.wpst-testimonial-dots');
        var progress = w.querySelector('.wpst-testimonial-progress>span');

        function stop(){
            if(timer){clearInterval(timer);timer=null;}
        }
        function restart(){
            stop();
            if(w.dataset.autoplay==='yes' && items.length>1 && !document.hidden){
                var speed=Math.max(1800,parseInt(w.dataset.speed||5000,10));
                timer=setInterval(function(){show(i+1,false)},speed);
            }
        }
        function syncProgress(){
            if(progress){
                progress.style.width=Math.min(100,((i+1)/Math.max(1,items.length))*100)+'%';
            }
        }
        function show(n,restartTimer){
            i = (n + items.length) % items.length;
            items.forEach(function(x,k){
                var on=k===i;
                x.classList.toggle('is-active',on);
                x.setAttribute('aria-hidden',on?'false':'true');
            });
            if(dots){
                all(dots,'button').forEach(function(b,k){
                    var on=k===i;
                    b.classList.toggle('is-active',on);
                    b.setAttribute('aria-current',on?'true':'false');
                });
            }
            syncProgress();
            if(restartTimer!==false)restart();
        }

        if(dots){
            dots.innerHTML='';
            items.forEach(function(_,idx){
                var b=document.createElement('button');
                b.type='button';
                b.setAttribute('aria-label','Yorum '+(idx+1));
                b.addEventListener('click',function(){show(idx,true)});
                dots.appendChild(b);
            });
        }

        var p = w.querySelector('.wpst-ew-testimonial-prev');
        var n = w.querySelector('.wpst-ew-testimonial-next');
        if(p) p.addEventListener('click',function(){ show(i-1,true); });
        if(n) n.addEventListener('click',function(){ show(i+1,true); });
        addSwipe(w,function(){show(i-1,true)},function(){show(i+1,true)});

        if(w.dataset.mouseDrag==='yes'){
            var dragging=false,dragStartX=0,dragLastX=0;
            w.addEventListener('mousedown',function(e){
                if(e.button!==0||e.target.closest('a,button,input,textarea,select'))return;
                dragging=true;dragStartX=e.clientX;dragLastX=e.clientX;
                w.classList.add('is-mouse-dragging');stop();
                e.preventDefault();
            });
            window.addEventListener('mousemove',function(e){if(dragging)dragLastX=e.clientX;});
            window.addEventListener('mouseup',function(){
                if(!dragging)return;
                var dx=dragLastX-dragStartX;
                dragging=false;w.classList.remove('is-mouse-dragging');
                if(Math.abs(dx)>=46)show(i+(dx<0?1:-1),true);else restart();
            });
            w.addEventListener('dragstart',function(e){e.preventDefault();});
        }

        w.setAttribute('role','region');
        w.setAttribute('aria-roledescription','carousel');
        w.addEventListener('keydown',function(e){
            if(e.key==='ArrowLeft'){e.preventDefault();show(i-1,true);}
            if(e.key==='ArrowRight'){e.preventDefault();show(i+1,true);}
            if(e.key==='Home'){e.preventDefault();show(0,true);}
            if(e.key==='End'){e.preventDefault();show(items.length-1,true);}
        });
        if(w.dataset.pauseHover==='yes'){
            w.addEventListener('mouseenter',stop);
            w.addEventListener('mouseleave',restart);
        }
        w.addEventListener('focusin',stop);
        w.addEventListener('focusout',function(){setTimeout(function(){if(!w.contains(document.activeElement))restart();},0);});
        document.addEventListener('visibilitychange',function(){if(document.hidden)stop();else restart();});

        show(0,false);
        restart();
    });
}

function initTabs(root){
    all(root,'.wpst-ew-tabs').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var buttons = all(w,'.wpst-ew-tabs-nav button');
        var panels = all(w,'.wpst-ew-tabs-panels article');

        function activate(idx,focus){
            buttons.forEach(function(x,i){
                var on=i===idx;
                x.classList.toggle('is-active',on);
                x.setAttribute('aria-selected',on?'true':'false');
                x.tabIndex=on?0:-1;
            });
            panels.forEach(function(x,i){
                var on=i===idx;
                x.classList.toggle('is-active',on);
                x.hidden=!on;
            });
            var btn=buttons[idx];
            if(focus&&btn)btn.focus();
            if(btn&&window.innerWidth<768&&btn.scrollIntoView)btn.scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'});
        }
        buttons.forEach(function(btn,idx){
            btn.addEventListener('click',function(){activate(idx,false);});
            btn.addEventListener('keydown',function(e){
                var next=idx;
                if(e.key==='ArrowRight')next=(idx+1)%buttons.length;
                else if(e.key==='ArrowLeft')next=(idx-1+buttons.length)%buttons.length;
                else if(e.key==='Home')next=0;
                else if(e.key==='End')next=buttons.length-1;
                else return;
                e.preventDefault();
                activate(next,true);
            });
        });
        activate(0,false);
    });
}

function initBeforeAfter(root){
    all(root,'.wpst-ew-before-after').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var input = w.querySelector('input[type="range"]');
        var after = w.querySelector('.wpst-ew-ba-after');
        if(!input || !after) return;

        function paint(){
            after.style.clipPath = 'inset(0 0 0 '+input.value+'%)';
        }
        input.addEventListener('input',paint);
        paint();
    });
}

function initCarousels(root){
    all(root,'.wpst-ew-image-carousel,.wpst-ew-card-carousel,.wpst-ew-service-carousel,.wpst-ew-team-carousel').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var track = w.querySelector('.wpst-ew-carousel-track,.wpst-ew-card-carousel-track,.wpst-team-pro');
        if(!track || !track.children.length) return;

        w.style.setProperty('--wpst-carousel-visible-desktop',w.dataset.visible||'3');
        w.style.setProperty('--wpst-carousel-visible-tablet',w.dataset.visibleTablet||'2');
        w.style.setProperty('--wpst-carousel-visible-mobile',w.dataset.visibleMobile||'1');
        if(w.dataset.fit)w.style.setProperty('--wpst-carousel-fit',w.dataset.fit);

        var index = 0;

        function visibleCount(){
            var width=window.innerWidth;
            var key=width<700?'visibleMobile':(width<1000?'visibleTablet':'visible');
            var fallback=width<700?1:(width<1000?2:3);
            return Math.max(1,parseInt(w.dataset[key]||fallback,10));
        }

        function maxIndex(){
            return Math.max(0, track.children.length - visibleCount());
        }

        function syncButtons(){
            if(prev)prev.disabled=index<=0;
            if(next)next.disabled=index>=maxIndex();
            w.classList.toggle('is-at-start',index<=0);
            w.classList.toggle('is-at-end',index>=maxIndex());
            var count=visibleCount();
            Array.prototype.forEach.call(track.children,function(item,i){
                item.setAttribute('aria-hidden',(i<index||i>=index+count)?'true':'false');
            });
            var progress=w.querySelector('.wpst-service-carousel-progress>span,.wpst-carousel-progress>span');
            if(progress){
                var total=Math.max(1,maxIndex()+1);
                var value=Math.min(100,((index+1)/total)*100);
                progress.style.width=value+'%';
            }
        }

        function move(delta){
            index = Math.max(0, Math.min(maxIndex(), index + delta));
            var first = track.children[0];
            if(!first) return;
            var styles=window.getComputedStyle(track);
            var gap=parseFloat(styles.columnGap||styles.gap||'18')||0;
            var width = first.getBoundingClientRect().width + gap;
            track.style.transform = 'translate3d('+(-index * width)+'px,0,0)';
            syncButtons();
        }

        var prev = w.querySelector('.wpst-ew-carousel-prev,.wpst-ew-card-prev,.wpst-team-carousel-prev');
        var next = w.querySelector('.wpst-ew-carousel-next,.wpst-ew-card-next,.wpst-team-carousel-next');
        if(prev) prev.addEventListener('click',function(){ move(-1); });
        if(next) next.addEventListener('click',function(){ move(1); });
        addSwipe(w,function(){move(-1)},function(){move(1)});

        if(w.dataset.mouseDrag==='yes' && track.dataset.wpstMouseDragReady!=='1'){
            track.dataset.wpstMouseDragReady='1';
            var dragStartX=0,dragLastX=0,dragging=false,dragMoved=false;

            track.addEventListener('mousedown',function(e){
                if(e.button!==0)return;
                dragStartX=e.clientX;
                dragLastX=e.clientX;
                dragging=true;
                dragMoved=false;
                w.classList.add('is-mouse-dragging');
                e.preventDefault();
            });

            window.addEventListener('mousemove',function(e){
                if(!dragging)return;
                dragLastX=e.clientX;
                if(Math.abs(dragLastX-dragStartX)>6)dragMoved=true;
            });

            window.addEventListener('mouseup',function(){
                if(!dragging)return;
                var delta=dragLastX-dragStartX;
                dragging=false;
                w.classList.remove('is-mouse-dragging');
                if(Math.abs(delta)>=42){
                    move(delta<0?1:-1);
                }
                setTimeout(function(){dragMoved=false;},0);
            });

            track.addEventListener('click',function(e){
                if(dragMoved){
                    e.preventDefault();
                    e.stopPropagation();
                }
            },true);

            track.addEventListener('dragstart',function(e){e.preventDefault();});
        }
        w.setAttribute('role','region');w.setAttribute('aria-roledescription','carousel');
        if(!w.hasAttribute('tabindex'))w.tabIndex=0;
        w.addEventListener('keydown',function(e){
            if(e.key==='ArrowLeft'){e.preventDefault();move(-1);}
            if(e.key==='ArrowRight'){e.preventDefault();move(1);}
            if(e.key==='Home'){e.preventDefault();index=0;move(0);}
            if(e.key==='End'){e.preventDefault();index=maxIndex();move(0);}
        });
        move(0);

        var resizeTimer=null;
        window.addEventListener('resize',function(){clearTimeout(resizeTimer);resizeTimer=setTimeout(function(){move(0)},100)});
    });
}

function initAnimatedHeading(root){
    all(root,'.wpst-ew-animated-heading').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var target = w.querySelector('.wpst-ew-animated-word');
        if(!target) return;

        var words = [];
        try{ words = JSON.parse(w.dataset.words || '[]'); }catch(e){}
        if(!words.length) return;

        var speed = Math.max(700,parseInt(w.dataset.speed || '2200',10));
        var transition = Math.max(120,parseInt(w.dataset.transition || '320',10));
        var type = w.dataset.animation || 'slide';
        var paused = false;
        var i = 0;
        var timer = null;

        w.style.setProperty('--wpst-ah-transition',(transition/1000)+'s');

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var mobileReduced = w.dataset.reduceMobile === '1' && window.matchMedia('(max-width:767px)').matches;
        if(reduce || mobileReduced){
            target.textContent = words[0];
            return;
        }

        if(w.dataset.pauseHover === '1'){
            w.addEventListener('mouseenter',function(){paused=true;});
            w.addEventListener('mouseleave',function(){paused=false;});
            w.addEventListener('focusin',function(){paused=true;});
            w.addEventListener('focusout',function(){paused=false;});
        }

        function setWord(word){
            if(type === 'typing'){
                target.textContent = '';
                var chars = Array.from(word);
                var ci = 0;
                var delay = Math.max(35,Math.min(90,transition/Math.max(1,chars.length)));
                function typeNext(){
                    if(ci >= chars.length) return;
                    target.textContent += chars[ci++];
                    setTimeout(typeNext,delay);
                }
                typeNext();
                return;
            }

            target.classList.add('is-out');
            setTimeout(function(){
                target.textContent = word;
                target.classList.remove('is-out');
                target.classList.add('is-in');
                setTimeout(function(){target.classList.remove('is-in');},transition);
            },Math.min(transition,260));
        }

        function tick(){
            if(!paused){
                i = (i + 1) % words.length;
                setWord(words[i]);
            }
            timer = setTimeout(tick,speed);
        }
        timer = setTimeout(tick,speed);
    });
}

function initAnimatedCounter(root){
    all(root,'.wpst-ew-animated-counter').forEach(function(w){
        if(w.dataset.wpstReady === '1') return;
        w.dataset.wpstReady = '1';

        var out = w.querySelector('strong');
        if(!out) return;

        var target = parseFloat(w.dataset.target || '0');
        var duration = parseInt(w.dataset.duration || '1600',10);
        var prefix = w.dataset.prefix || '';
        var suffix = w.dataset.suffix || '';

        function run(){
            var start = performance.now();
            function frame(now){
                var p = Math.min(1,(now-start)/duration);
                var eased = 1 - Math.pow(1-p,3);
                var current = Math.round(target * eased);
                out.textContent = prefix + current + suffix;
                if(p < 1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        }

        if('IntersectionObserver' in window){
            var io = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        io.disconnect();
                        run();
                    }
                });
            },{threshold:.35});
            io.observe(w);
        }else run();
    });
}

function initReveal(root){
    all(root,'.wpst-animate-on-view').forEach(function(el){
        if(el.dataset.wpstObserved === '1') return;
        el.dataset.wpstObserved = '1';

        if('IntersectionObserver' in window){
            var io = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            },{threshold:.18});
            io.observe(el);
        }else{
            el.classList.add('is-visible');
        }
    });
}


function initParallax(root){
 all(root,'.wpst-ew-parallax-image').forEach(function(w){
  if(w.dataset.wpstReady==='1')return;w.dataset.wpstReady='1';
  var img=w.querySelector('img,.wpst-ew-parallax-placeholder');if(!img)return;
  var strength=parseFloat(w.dataset.strength||'28');
  function paint(){
   if((w.dataset.disableMobile==='1' && window.matchMedia('(max-width:767px)').matches) || window.matchMedia('(prefers-reduced-motion: reduce)').matches){img.style.transform='';return;}
   var r=w.getBoundingClientRect(),vh=window.innerHeight||1,p=(r.top+r.height/2-vh/2)/vh;
   img.style.transform='translate3d(0,'+(-p*strength)+'px,0) scale(1.04)';
  }
  window.addEventListener('scroll',paint,{passive:true});window.addEventListener('resize',paint);paint();
 });
}
function initScrollProgress(root){
 all(root,'.wpst-ew-scroll-progress').forEach(function(w){
  if(w.dataset.wpstReady==='1')return;w.dataset.wpstReady='1';
  var bar=w.querySelector('span'),label=w.querySelector('b');if(!bar)return;
  var ticking=false;
  function paint(){
   ticking=false;
   var d=document.documentElement,max=Math.max(1,d.scrollHeight-d.clientHeight);
   var value=Math.min(100,Math.max(0,(d.scrollTop/max)*100));
   var rounded=Math.round(value);
   bar.style.width=value+'%';
   w.setAttribute('aria-valuenow',String(rounded));
   if(label)label.textContent=rounded+'%';
  }
  function requestPaint(){if(ticking)return;ticking=true;requestAnimationFrame(paint);}
  document.addEventListener('scroll',requestPaint,{passive:true});
  window.addEventListener('resize',requestPaint,{passive:true});
  paint();
 });
}
function initMouseCards(root){
 all(root,'.wpst-ew-mouse-card').forEach(function(w){
  if(w.dataset.wpstReady==='1')return;w.dataset.wpstReady='1';var glow=w.querySelector('.wpst-ew-mouse-glow');
  w.addEventListener('mousemove',function(e){var r=w.getBoundingClientRect(),x=e.clientX-r.left,y=e.clientY-r.top,rx=((y/r.height)-.5)*-8,ry=((x/r.width)-.5)*8;w.style.transform='perspective(900px) rotateX('+rx+'deg) rotateY('+ry+'deg)';if(glow){glow.style.left=x+'px';glow.style.top=y+'px';}});
  w.addEventListener('mouseleave',function(){w.style.transform='';});
 });
}

function initSpotlight(root){
 all(root,'.wpst-ew-hero-spotlight').forEach(function(el){
  if(el.dataset.wpstSpotlight)return;el.dataset.wpstSpotlight='1';
  el.addEventListener('pointermove',function(e){var r=el.getBoundingClientRect();el.style.setProperty('--mx',(e.clientX-r.left)+'px');el.style.setProperty('--my',(e.clientY-r.top)+'px');});
 });
}
function initScrollRevealText(root){
 all(root,'.wpst-ew-scroll-reveal-text').forEach(function(el){
  if(el.dataset.wpstRevealText)return;el.dataset.wpstRevealText='1';var words=all(el,'p span');
  if(!('IntersectionObserver'in window)){words.forEach(function(w){w.classList.add('is-revealed')});return;}
  var io=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){words.forEach(function(w,i){setTimeout(function(){w.classList.add('is-revealed')},i*35)});io.disconnect();}})},{threshold:.2});io.observe(el);
 });
}
function initCountdown(root){
 all(root,'.wpst-ew-countdown-modern').forEach(function(el){
  if(el.dataset.wpstCountdown)return;el.dataset.wpstCountdown='1';var target=new Date((el.dataset.date||'').replace(' ','T')).getTime();
  function tick(){var diff=Math.max(0,target-Date.now()),d=Math.floor(diff/86400000),h=Math.floor(diff%86400000/3600000),m=Math.floor(diff%3600000/60000),s=Math.floor(diff%60000/1000);[['[data-d]',d],['[data-h]',h],['[data-m]',m],['[data-s]',s]].forEach(function(x){var n=el.querySelector(x[0]);if(n)n.textContent=String(x[1]).padStart(2,'0')});} tick();setInterval(tick,1000);
 });
}


function initImageReveal(root){
 all(root,'.wpst-image-reveal').forEach(function(el){
  if(el.dataset.wpstRevealReady)return;el.dataset.wpstRevealReady='1';
  if(!('IntersectionObserver'in window)){el.classList.add('is-visible');return;}
  var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){el.classList.add('is-visible');io.disconnect();}})},{threshold:.18});io.observe(el);
 });
}
function initModal(root){
 all(root,'.wpst-modal-widget').forEach(function(w){
  if(w.dataset.wpstModalReady)return;w.dataset.wpstModalReady='1';
  var trigger=w.querySelector('[data-modal-open]'),overlay=w.querySelector('.wpst-modal-overlay');if(!trigger||!overlay)return;
  var dialog=overlay.querySelector('[role="dialog"]'),lastFocus=null;
  function firstFocusable(){return overlay.querySelector('[data-modal-close],button:not([disabled]),a[href],input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])');}
  function open(){
   lastFocus=document.activeElement;
   overlay.classList.add('is-open');overlay.setAttribute('aria-hidden','false');document.documentElement.classList.add('wpst-modal-open');
   window.setTimeout(function(){var f=firstFocusable();if(f)f.focus();else if(dialog){dialog.setAttribute('tabindex','-1');dialog.focus();}},0);
  }
  function close(){
   overlay.classList.remove('is-open');overlay.setAttribute('aria-hidden','true');document.documentElement.classList.remove('wpst-modal-open');
   if(lastFocus&&typeof lastFocus.focus==='function')window.setTimeout(function(){lastFocus.focus();},0);
  }
  trigger.addEventListener('click',open);all(overlay,'[data-modal-close]').forEach(function(b){b.addEventListener('click',close)});
  overlay.addEventListener('click',function(e){if(e.target===overlay)close()});
  document.addEventListener('keydown',function(e){
   if(!overlay.classList.contains('is-open'))return;
   if(e.key==='Escape'){e.preventDefault();close();return;}
   if(e.key==='Tab'){
    var focusables=all(overlay,'button:not([disabled]),a[href],input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])').filter(function(el){return el.offsetParent!==null;});
    if(!focusables.length)return;
    var first=focusables[0],last=focusables[focusables.length-1];
    if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
    else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
   }
  });
 });
}
function initContentSlider(root){
 all(root,'.wpst-content-slider').forEach(function(w){
  if(w.dataset.wpstSliderReady)return;
  w.dataset.wpstSliderReady='1';
  var slides=all(w,'.wpst-content-slide'),dots=w.querySelector('.wpst-content-slider-dots'),index=0,timer=null;
  var progress=w.querySelector('.wpst-content-slider-progress>span');
  if(!slides.length)return;

  function stop(){if(timer){clearInterval(timer);timer=null;}}
  function restart(){
   stop();
   if(w.dataset.autoplay==='yes' && slides.length>1 && !document.hidden){
    var speed=Math.max(1500,parseInt(w.dataset.speed||4500,10));
    timer=setInterval(function(){show(index+1,false)},speed);
   }
  }
  function show(i,restartTimer){
   index=(i+slides.length)%slides.length;
   slides.forEach(function(s,n){
    var on=n===index;
    s.classList.toggle('is-active',on);
    s.setAttribute('aria-hidden',on?'false':'true');
   });
   all(dots,'button').forEach(function(b,n){
    var on=n===index;
    b.classList.toggle('is-active',on);
    b.setAttribute('aria-current',on?'true':'false');
   });
   if(progress)progress.style.width=Math.min(100,((index+1)/Math.max(1,slides.length))*100)+'%';
   if(restartTimer!==false)restart();
  }

  if(dots){
   dots.innerHTML='';
   slides.forEach(function(_,i){
    var b=document.createElement('button');
    b.type='button';
    b.setAttribute('aria-label','Slayt '+(i+1));
    b.addEventListener('click',function(){show(i,true)});
    dots.appendChild(b);
   });
  }

  var prev=w.querySelector('[data-slider-prev]'),next=w.querySelector('[data-slider-next]');
  if(prev)prev.addEventListener('click',function(){show(index-1,true)});
  if(next)next.addEventListener('click',function(){show(index+1,true)});
  addSwipe(w,function(){show(index-1,true)},function(){show(index+1,true)});

  if(w.dataset.mouseDrag==='yes'){
   var dragStartX=0,dragLastX=0,dragging=false;
   w.addEventListener('mousedown',function(e){
    if(e.button!==0||e.target.closest('a,button,input,textarea,select'))return;
    dragStartX=e.clientX;dragLastX=e.clientX;dragging=true;
    w.classList.add('is-mouse-dragging');
    stop();
   });
   window.addEventListener('mousemove',function(e){if(dragging)dragLastX=e.clientX;});
   window.addEventListener('mouseup',function(){
    if(!dragging)return;
    var dx=dragLastX-dragStartX;
    dragging=false;
    w.classList.remove('is-mouse-dragging');
    if(Math.abs(dx)>=46)show(index+(dx<0?1:-1),true);
    else restart();
   });
  }

  if(!w.hasAttribute('tabindex'))w.tabIndex=0;
  w.addEventListener('keydown',function(e){
   if(e.key==='ArrowLeft'){e.preventDefault();show(index-1,true);}
   if(e.key==='ArrowRight'){e.preventDefault();show(index+1,true);}
   if(e.key==='Home'){e.preventDefault();show(0,true);}
   if(e.key==='End'){e.preventDefault();show(slides.length-1,true);}
  });
  if(w.dataset.pauseHover==='yes'){
   w.addEventListener('mouseenter',stop);
   w.addEventListener('mouseleave',restart);
  }
  w.addEventListener('focusin',stop);
  w.addEventListener('focusout',function(){setTimeout(function(){if(!w.contains(document.activeElement))restart();},0);});
  document.addEventListener('visibilitychange',function(){if(document.hidden)stop();else restart();});

  w.setAttribute('role','region');
  w.setAttribute('aria-roledescription','carousel');
  show(0,false);
  restart();
 });
}


function initWPSTNavigation(root){
 all(root,'[data-wpst-nav]').forEach(function(nav){
  if(nav.dataset.wpstNavReady)return; nav.dataset.wpstNavReady='1';
  var toggle=nav.querySelector('.wpst-nav-toggle'),panel=nav.querySelector('.wpst-nav-mobile-panel'),close=nav.querySelector('.wpst-nav-close'),overlay=nav.querySelector('.wpst-nav-overlay');
  if(!toggle||!panel)return;
  function setFixedScope(active){
   var node=nav.parentElement;
   while(node&&node!==document.body){
    if(active)node.classList.add('wpst-nav-fixed-scope');
    else node.classList.remove('wpst-nav-fixed-scope');
    node=node.parentElement;
   }
  }
  function openMenu(){setFixedScope(true);nav.classList.add('is-open');document.documentElement.classList.add('wpst-nav-lock');toggle.setAttribute('aria-expanded','true');}
  function closeMenu(){nav.classList.remove('is-open');document.documentElement.classList.remove('wpst-nav-lock');toggle.setAttribute('aria-expanded','false');setFixedScope(false);}
  toggle.addEventListener('click',function(){nav.classList.contains('is-open')?closeMenu():openMenu();});
  if(close)close.addEventListener('click',closeMenu);
  if(overlay)overlay.addEventListener('click',closeMenu);
  nav.querySelectorAll('.menu-item-has-children').forEach(function(li){
   var a=li.querySelector(':scope > a'),sub=li.querySelector(':scope > .sub-menu'); if(!a||!sub)return;
   var b=document.createElement('button'); b.type='button'; b.className='wpst-nav-subtoggle'; b.setAttribute('aria-expanded','false'); b.setAttribute('aria-label','Alt menüyü aç'); b.innerHTML='<span></span>'; a.insertAdjacentElement('afterend',b);
   b.addEventListener('click',function(){var o=li.classList.toggle('is-sub-open');b.setAttribute('aria-expanded',o?'true':'false');});
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&nav.classList.contains('is-open'))closeMenu();});
  window.addEventListener('resize',function(){if(window.innerWidth>1024)closeMenu();},{passive:true});
 });
}

function bootWPSTNavigationDOM(){
  initWPSTNavigation(document);
}

function initReviewsCarousel(root){
 all(root,'.wpst-reviews-carousel').forEach(function(w){
  if(w.dataset.wpstReviewsReady)return;
  w.dataset.wpstReviewsReady='1';

  var viewport=w.querySelector('.wpst-reviews-carousel-viewport');
  var track=w.querySelector('.wpst-reviews-carousel-track');
  var cards=all(w,'.wpst-review-carousel-card');
  var dots=w.querySelector('.wpst-reviews-carousel-dots');
  var prev=w.querySelector('.wpst-reviews-prev');
  var next=w.querySelector('.wpst-reviews-next');
  if(!viewport||!track||!cards.length)return;

  var index=0,timer=null,dragging=false,startX=0,lastX=0;

  function visible(){
   var width=window.innerWidth||1200;
   if(width<=767)return Math.max(1,parseInt(w.dataset.visibleMobile||1,10));
   if(width<=1024)return Math.max(1,parseInt(w.dataset.visibleTablet||2,10));
   return Math.max(1,parseInt(w.dataset.visibleDesktop||3,10));
  }
  function maxIndex(){return Math.max(0,cards.length-visible());}
  function clampIndex(){index=Math.max(0,Math.min(index,maxIndex()));}
  function buildDots(){
   if(!dots)return;
   dots.innerHTML='';
   var count=maxIndex()+1;
   for(var i=0;i<count;i++){
    (function(n){
     var b=document.createElement('button');
     b.type='button';
     b.setAttribute('aria-label','Yorum grubu '+(n+1));
     b.addEventListener('click',function(){go(n,true)});
     dots.appendChild(b);
    })(i);
   }
  }
  function layout(){
   var v=visible();
   w.style.setProperty('--wpst-reviews-visible',v);
   clampIndex();
   buildDots();
   paint(false);
  }
  function paint(restart){
   var v=visible();
   var gap=parseFloat(getComputedStyle(w).getPropertyValue('--wpst-reviews-gap'))||20;
   var cardWidth=(viewport.clientWidth-gap*(v-1))/v;
   cards.forEach(function(card){card.style.flex='0 0 '+Math.max(0,cardWidth)+'px';});
   var x=(cardWidth+gap)*index;
   track.style.transform='translate3d('+(-x)+'px,0,0)';
   all(dots,'button').forEach(function(b,n){
    b.classList.toggle('is-active',n===index);
    b.setAttribute('aria-current',n===index?'true':'false');
   });
   if(prev)prev.disabled=index<=0;
   if(next)next.disabled=index>=maxIndex();
   if(restart!==false)startAuto();
  }
  function go(n,restart){
   index=Math.max(0,Math.min(n,maxIndex()));
   paint(restart);
  }
  function stopAuto(){if(timer){clearInterval(timer);timer=null;}}
  function startAuto(){
   stopAuto();
   if(w.dataset.autoplay==='yes' && cards.length>visible() && !document.hidden){
    var speed=Math.max(1500,parseInt(w.dataset.speed||5000,10));
    timer=setInterval(function(){
     if(index>=maxIndex())go(0,false);else go(index+1,false);
    },speed);
   }
  }

  if(prev)prev.addEventListener('click',function(){go(index-1,true)});
  if(next)next.addEventListener('click',function(){go(index+1,true)});

  addSwipe(w,function(){go(index-1,true)},function(){go(index+1,true)});

  if(w.dataset.mouseDrag==='yes'){
   viewport.addEventListener('mousedown',function(e){
    if(e.button!==0||e.target.closest('a,button,input,textarea,select'))return;
    dragging=true;startX=e.clientX;lastX=e.clientX;
    w.classList.add('is-mouse-dragging');
    stopAuto();
    e.preventDefault();
   });
   window.addEventListener('mousemove',function(e){if(dragging)lastX=e.clientX;});
   window.addEventListener('mouseup',function(){
    if(!dragging)return;
    var dx=lastX-startX;
    dragging=false;w.classList.remove('is-mouse-dragging');
    if(Math.abs(dx)>=42)go(index+(dx<0?1:-1),true);else startAuto();
   });
   viewport.addEventListener('dragstart',function(e){e.preventDefault()});
  }

  w.addEventListener('keydown',function(e){
   if(e.key==='ArrowLeft'){e.preventDefault();go(index-1,true);}
   if(e.key==='ArrowRight'){e.preventDefault();go(index+1,true);}
   if(e.key==='Home'){e.preventDefault();go(0,true);}
   if(e.key==='End'){e.preventDefault();go(maxIndex(),true);}
  });

  if(w.dataset.pauseHover==='yes'){
   w.addEventListener('mouseenter',stopAuto);
   w.addEventListener('mouseleave',startAuto);
  }
  w.addEventListener('focusin',stopAuto);
  w.addEventListener('focusout',function(){setTimeout(function(){if(!w.contains(document.activeElement))startAuto();},0);});
  document.addEventListener('visibilitychange',function(){if(document.hidden)stopAuto();else startAuto();});

  var resizeRaf=0;
  window.addEventListener('resize',function(){
   cancelAnimationFrame(resizeRaf);
   resizeRaf=requestAnimationFrame(layout);
  },{passive:true});

  w.setAttribute('role','region');
  w.setAttribute('aria-roledescription','carousel');
  layout();
  startAuto();
 });
}

function initAdvancedButtons(root){
 all(root,'.wpst-adv-button.effect-magnetic').forEach(function(b){
  if(b.dataset.wpstMagnetic)return;b.dataset.wpstMagnetic='1';
  b.addEventListener('mousemove',function(e){var r=b.getBoundingClientRect(),x=(e.clientX-r.left-r.width/2)*.13,y=(e.clientY-r.top-r.height/2)*.18;b.style.transform='translate('+x+'px,'+y+'px)'});
  b.addEventListener('mouseleave',function(){b.style.transform=''});
 });
}


function initDisclosures(root){
 all(root,'.wpst-ew-faq details,.wpst-adv-accordion details').forEach(function(d){
  if(d.dataset.wpstDisclosureReady==='1')return;
  d.dataset.wpstDisclosureReady='1';
  var summary=d.querySelector('summary');
  if(!summary)return;
  function sync(){summary.setAttribute('aria-expanded',d.open?'true':'false');}
  d.addEventListener('toggle',sync);
  sync();
 });
}

function initAll(root){
    initImageSlider(root);
    initHeroSlider(root);
    initTestimonials(root);
    initTabs(root);
    initBeforeAfter(root);
    initCarousels(root);
    initAnimatedHeading(root);
    initAnimatedCounter(root);
    initReveal(root);
    initParallax(root);
    initScrollProgress(root);
    initMouseCards(root);
    initSpotlight(root);
    initScrollRevealText(root);
    initCountdown(root);
    initImageReveal(root);
    initModal(root);
    initContentSlider(root);
    initReviewsCarousel(root);
    initWPSTNavigation(root);
    initDisclosures(root);
    initAdvancedButtons(root);
}

function bindElementorHooks(){
    if(!window.elementorFrontend || !window.elementorFrontend.hooks) return false;

    var hook = window.elementorFrontend.hooks;
    var names = [
        'wpsoft-hero-slider.default',
        'wpsoft-image-carousel.default',
        'wpsoft-testimonial-slider.default',
        'wpsoft-tabs-modern.default',
        'wpsoft-before-after.default',
        'wpsoft-card-carousel.default',
        'wpsoft-animated-heading.default',
        'wpsoft-animated-counter.default',
        'wpsoft-reveal-cards.default',
        'wpsoft-image-slider.default',
        'wpsoft-team-carousel-pro.default',
        'wpsoft-faq.default',
        'wpsoft-advanced-accordion.default',
        'wpsoft-advanced-button.default','wpsoft-image-reveal.default','wpsoft-modal.default','wpsoft-content-slider.default','wpsoft-reviews-carousel.default','wpsoft-navigation.default'
    ];

    names.forEach(function(name){
        hook.addAction('frontend/element_ready/'+name,function($scope){
            initAll($scope && $scope[0] ? $scope[0] : document);
        });
    });

    hook.addAction('frontend/element_ready/global',function($scope){
        initAll($scope && $scope[0] ? $scope[0] : document);
    });

    return true;
}

function boot(){
    initAll(document);

    if(!bindElementorHooks()){
        var tries = 0;
        var timer = setInterval(function(){
            tries++;
            if(bindElementorHooks() || tries > 16){
                clearInterval(timer);
            }
        },250);
    }

    // DOM observation is expensive on normal pages. Keep it only for Elementor edit/preview mode,
    // where widgets can be re-rendered without a full page refresh.
    var isElementorEdit = !!(
        window.elementorFrontend &&
        typeof window.elementorFrontend.isEditMode === 'function' &&
        window.elementorFrontend.isEditMode()
    );
    if(isElementorEdit && 'MutationObserver' in window){
        var pending = null;
        var observer = new MutationObserver(function(){
            clearTimeout(pending);
            pending = setTimeout(function(){ initAll(document); },100);
        });
        observer.observe(document.body||document.documentElement,{childList:true,subtree:true});
    }
}

if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded',boot);
}else{
    boot();
}

window.WPSTInitWidgets = initAll;

})();

/* Stabilization 3: Navigation re-initialization is handled by initAll() + Elementor hooks. */


/* FAQ 2.0 · optional single-open behavior */
(function(){
 function initFaq(root){
  (root||document).querySelectorAll('.wpst-ew-faq[data-single-open="yes"]').forEach(function(faq){
   if(faq.dataset.wpstSingleReady==='1')return;
   faq.dataset.wpstSingleReady='1';
   faq.querySelectorAll('details').forEach(function(item){
    item.addEventListener('toggle',function(){
     if(!item.open)return;
     faq.querySelectorAll('details[open]').forEach(function(other){if(other!==item)other.removeAttribute('open');});
    });
   });
  });
 }
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){initFaq(document)});else initFaq(document);
 window.addEventListener('elementor/frontend/init',function(){
  if(window.elementorFrontend&&elementorFrontend.hooks){
   elementorFrontend.hooks.addAction('frontend/element_ready/wpsoft-faq.default',function($scope){initFaq($scope&&$scope[0]?$scope[0]:document)});
  }
 });
})();
