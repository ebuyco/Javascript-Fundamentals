class Range extends React.Component {
  constructor(props) {
    super(props);
    this.updateRange = this.updateRange.bind(this);
  }
  
  updateRange(e) {
    this.props.updateRange(e.target.value);
  }
  
  render() {
    // console.log(this.props);
    const { range } = this.props;
    return (
      <div>
        <input id="range" type="range"
          value={range}
          min="0"
          max="20"
          step="1"
          onChange={this.updateRange}
        />
        <span id="output">{range}</span>
      </div>
    )
  }
}

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      rangeVal: 0
    }
    this.updateRange = this.updateRange.bind(this);
  }
  
  updateRange(val) {
    this.setState({
      rangeVal: val
    })
  } 
  
  render() {
    const { rangeVal } = this.state;
    return (
      <Range range={rangeVal} updateRange={this.updateRange}/>
    )
  }  
}

const root = document.getElementById('root');
ReactDOM.render(<App />, root);