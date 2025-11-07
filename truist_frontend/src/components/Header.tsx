export default function Header(){
  return (
    <header className="bg-[#114b66]">
      <div className="max-w-6xl mx-auto">
        <div className="h-20 flex items-center justify-center px-6">
          <img
            src="/assets/Logo-Dark-Background.png"
            alt="BlueTrust Bank"
            className="h-10 w-auto"
          />
        </div>
      </div>
      <div className="bg-white/90">
        <div className="max-w-6xl mx-auto px-6 py-2 text-xs text-slate-600">
          <div className="flex items-center gap-3 italic">
            <img
              src="/assets/FDIC_Logo_blue.svg"
              alt="FDIC"
              className="h-6 w-auto"
            />
            <span>FDIC-Insured - Backed by the full faith and credit of the U.S. Government</span>
          </div>
        </div>
      </div>
    </header>
  )
}