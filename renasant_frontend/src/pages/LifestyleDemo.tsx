import React, {useState} from 'react'
import Footer from '../components/Footer'
import Header from '../components/Header'

export default function LoginForm(){
  const [remember,setRemember] = useState(true)
  const [show,setShow] = useState(false)

  return (

    <div className="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
      <Header />

      <main className="flex-1 flex items-center justify-center px-4 py-20">
        <div className="w-full max-w-md">
          <div className="bg-white rounded-md p-8 login-card shadow-lg shadow-slate-900/10">
            <h2 className="text-center text-2xl font-semibold mb-6 text-slate-700">Login to Online Banking</h2>

            <div className="space-y-4">
              <div>
                <label className="block text-sm text-slate-500 mb-1">User ID</label>
                <div className="flex items-center border border-slate-200 rounded">
                  <input className="flex-1 px-3 py-3 text-sm focus:outline-none" placeholder="User ID" />
                  <button className="px-3 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-..."/></svg>
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-sm text-slate-500 mb-1">Password</label>
                <div className="flex items-center border border-slate-200 rounded">
                  <input type={show? 'text':'password'} className="flex-1 px-3 py-3 text-sm focus:outline-none" placeholder="Password" />
                  <button onClick={()=>setShow(s=>!s)} className="px-3 text-slate-400">
                    {show ? 'Hide' : 'Show'}
                  </button>
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <input id="remember" type="checkbox" checked={remember} onChange={()=>setRemember(r=>!r)} className="h-4 w-4 text-blue-600" />
                <label htmlFor="remember" className="text-sm text-slate-700">Remember Me</label>
              </div>

              <button className="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 11c0-1.657 1.343-3 3-3s3 1.343 3 3v1H6v-1c0-1.657 1.343-3 3-3s3 1.343 3 3z" /></svg>
                <span>Login</span>
              </button>

              <div className="text-center">
                <a href="#" className="text-sm text-[#0f4f6c] hover:underline">Trouble logging in?</a>
              </div>

              <div className="text-center">
                <a href="#" className="inline-block text-sm border border-slate-300 rounded px-4 py-2">Enroll in Online Banking</a>
              </div>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  )
}