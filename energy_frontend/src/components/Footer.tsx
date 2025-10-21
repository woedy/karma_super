export default function Footer(){
  return (
    <footer className="bg-[#2f3136] text-white w-full">
      <div className="max-w-5xl mx-auto px-6 py-10 flex flex-col items-center gap-6">
        <nav className="flex flex-wrap items-center justify-center gap-4 text-sm text-[#74b8ff]">
          <a className="hover:underline" href="#">Contact Us</a>
          <span className="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
          <a className="hover:underline" href="#">Privacy &amp; Security</a>
          <span className="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
          <a className="hover:underline" href="#">Accessibility</a>
        </nav>
        <div className="flex items-center gap-3 text-sm text-[#74b8ff]">
          <div className="flex items-center justify-center w-7 h-7 border border-[#74b8ff] rounded-full text-xs font-semibold">
            <span>🏠</span>
          </div>
          <span>Equal Housing Lender</span>
        </div>
        <img src="/assets/ncua.png" alt="NCUA" className="h-16 w-auto" />
        <p className="text-xs text-gray-400">Federally insured by NCUA</p>
      </div>
    </footer>
  )
}