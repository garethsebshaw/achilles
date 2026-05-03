import Tool from './pages/Tool'

Nova.inertia('SocialLogin', Tool)

Nova.booting((app, store) => {
    Nova.inertia('SocialLogin', Tool) 
})
