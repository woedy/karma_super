import React from 'react'

export default function Header(){
  return (
    <header className="bg-[#114b66]">
      <div className="max-w-6xl mx-auto">
        <div className="h-20 flex items-center px-6">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-white/10 rounded flex items-center justify-center text-white font-semibold">BT</div>
            <div className="text-white">
              <div className="text-lg font-semibold">BlueTrust Bank</div>
            </div>
          </div>
        </div>
      </div>
      <div className="bg-white/90">
        <div className="max-w-6xl mx-auto px-6 py-2 text-xs text-slate-600">FDIC-Insured - Backed by the full faith and credit of the U.S. Government</div>
      </div>
    </header>
  )
}