import React from 'react'

export default function Footer(){
  return (
    <footer className="bg-[#114b66] text-white mt-8">
      <div className="max-w-6xl mx-auto px-6 py-8">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <div className="text-sm font-semibold">© BlueTrust Bank</div>
            <div className="text-xs mt-2">Member FDIC</div>
          </div>
          <div className="flex flex-wrap gap-3 text-sm">
            <a className="underline" href="#">Accessibility</a>
            <a className="underline" href="#">Mobile Privacy</a>
            <a className="underline" href="#">Privacy Statement</a>
            <a className="underline" href="#">Digital Banking Agreement</a>
          </div>
        </div>
      </div>
    </footer>
  )
}